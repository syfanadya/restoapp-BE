<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Table;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;

class ApiOrderController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $userId = $request->input('user_id');
        $limit = $request->input('limit', 10);

        $query = Order::query();

        if ($id) {
            $order = $query->with(['orderItems.food', 'table', 'user'])->find($id);

            if ($order) {
                return ResponseFormatter::success($order, 'Order found');
            }

            return ResponseFormatter::error('Order not found', 404);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $orders = $query->with(['orderItems.food', 'table', 'user'])->paginate($limit);

        return ResponseFormatter::success($orders, 'Orders retrieved');
    }

    public function create(CreateOrderRequest $request)
    {
        DB::beginTransaction();

        try {
            $table = Table::findOrFail($request->table_id);
            if ($table->status !== 'available') {
                return ResponseFormatter::error("Table '{$table->status}' not available", 400);
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => $request->input('customer_name'),
                'table_id' => $request->input('table_id'),
                'status' => 'pending',
                'total_price' => 0,
            ]);

            $totalPrice = 0;
            if ($request->has('order_items') && is_array($request->order_items)) {
                foreach ($request->order_items as $item) {
                    $orderItem = new OrderItem([
                        'food_id' => $item['food_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                    $order->orderItems()->save($orderItem);
                    $totalPrice += $item['quantity'] * $item['price'];
                }
            }

            $order->total_price = $totalPrice;
            $order->save();

            $table->update(['status' => 'occupied']);

            DB::commit();

            return ResponseFormatter::success($order->load('orderItems'), 'Order created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateOrderRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);

            if ($request->has('customer_name')) {
                $order->customer_name = $request->input('customer_name');
            }

            if ($request->has('table_id') && $request->input('table_id') != $order->table_id) {
                $newTable = Table::findOrFail($request->input('table_id'));

                if ($newTable->status !== 'available') {
                    return ResponseFormatter::error("Table '{$newTable->status}' not available", 400);
                }
                $oldTable = Table::find($order->table_id);
                if ($oldTable) {
                    $oldTable->update(['status' => 'available']);
                }
                $order->table_id = $newTable->id;
                $newTable->update(['status' => 'occupied']);
            }

            $order->save();

            DB::commit();

            return ResponseFormatter::success($order->load('orderItems'), 'Order updated successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);
            $order->orderItems()->delete();
            $table = Table::find($order->table_id);
            if ($table) {
                $table->update(['status' => 'available']);
            }

            $order->delete();

            DB::commit();

            return ResponseFormatter::success(null, 'Order and related order items deleted successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
