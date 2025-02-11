<?php

namespace App\Http\Controllers;

use App\Models\ItemOrder;
use App\Models\Order;
use App\Models\Product;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\ApproveOrderRequest;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function createOrder(CreateOrderRequest $request): \Illuminate\Http\JsonResponse
    {
        $order = Order::query()->create([
            'user_id' => $request->user_id,
            'status' => Order::STATUSES['PENDING'],
        ]);

        foreach ($request->products as $p) {
            $product = Product::query()->find($p['id']);
            if ($product->quantity < $p['quantity']) {
                throw ValidationException::withMessages([
                    'products' => ['На складе недостаточно товара.'],
                ]);
            }

            ItemOrder::query()->create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'price' => $product->price * $p['quantity'],
                'quantity' => $p['quantity'],
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function approveOrder(ApproveOrderRequest $request): \Illuminate\Http\JsonResponse
    {
        $order = Order::query()->find($request->order_id);
        foreach ($order->items as $item) {
            $order->user->balance -= $item->price;
            $item->product->decrement('quantity', $item->quantity);
            $item->product->save();
        }

        $order->user->save();

        $order->update([
            'status' => Order::STATUSES['APPROVED'],
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}
