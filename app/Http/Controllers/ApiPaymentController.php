<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreatePaymentRequest;

class ApiPaymentController extends Controller
{
    public function create(CreatePaymentRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $order = Order::findOrFail($data['order_id']);

            $data['user_id'] = Auth::id();

            // Hitung change manual tanpa if-else
            $change = $data['amount'] - $order->total_price;
            $data['change'] = $change > 0 ? $change : 0;

            if (!isset($data['status'])) {
                $data['status'] = 'unpaid';
            }

            $payment = Payment::create($data);

            if ($payment && $payment->status === 'paid' && $order->table) {
                $order->table->status = 'available';
                $order->status = 'completed';
                $order->table->save();
                $order->save();
            }

            DB::commit();

            return ResponseFormatter::success($payment, 'Payment created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
