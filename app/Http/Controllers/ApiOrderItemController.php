<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Food;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use GuzzleHttp\Promise\Create;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateOrderItemRequest;
use App\Http\Requests\UpdateOrderItemRequest;

class ApiOrderItemController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $orderId = $request->input('order_id');
        $limit = $request->input('limit', 10);

        $query = OrderItem::query();

        if ($id) {
            $orderItem = $query->with(['food', 'order'])->find($id);

            if ($orderItem) {
                return ResponseFormatter::success($orderItem, 'Order item found');
            }

            return ResponseFormatter::error('Order item not found', 404);
        }

        if ($orderId) {
            $query->where('order_id', $orderId);
        }

        $orderItems = $query->with(['food', 'order'])->paginate($limit);

        return ResponseFormatter::success($orderItems, 'Order items retrieved');
    }

    public function create(CreateOrderItemRequest $request)
    {
        DB::beginTransaction();

        try {
            $food = Food::findOrFail($request->food_id);

            $orderItem = OrderItem::create([
                'order_id' => $request->order_id,
                'food_id' => $food->id,
                'quantity' => $request->quantity,
                'price' => $food->price,
            ]);

            if (!$orderItem) {
                throw new Exception('Item not created');
            }

            $order = $orderItem->order()->lockForUpdate()->first();
            $totalPrice = $order->orderItems()->sum(DB::raw('quantity * price'));

            $order->status = 'progress';
            $order->total_price = $totalPrice;
            $order->save();

            $orderItem->load('food', 'order');

            DB::commit();

            return ResponseFormatter::success($orderItem, 'Item created and order updated');
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateOrderItemRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $orderItem = OrderItem::findOrFail($id);

            $orderItem->update([
                'quantity' => $request->quantity,
            ]);

            $order = $orderItem->order()->lockForUpdate()->first();

            $totalPrice = $order->orderItems()->sum(DB::raw('quantity * price'));

            $order->status = $totalPrice > 0 ? 'progress' : 'pending';
            $order->total_price = $totalPrice;
            $order->save();

            $orderItem->load('food', 'order');

            DB::commit();

            return ResponseFormatter::success($orderItem, 'Order item updated and order updated');
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $orderItem = OrderItem::findOrFail($id);
            $order = $orderItem->order()->lockForUpdate()->first();
            $orderItem->delete();
            $totalPrice = $order->orderItems()->sum(DB::raw('quantity * price'));
            $order->status = $totalPrice > 0 ? 'progress' : 'pending';
            $order->total_price = $totalPrice;
            $order->save();
            DB::commit();
            return ResponseFormatter::success(null, 'Order item deleted and order updated');
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
