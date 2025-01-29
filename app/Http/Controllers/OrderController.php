<?php

namespace App\Http\Controllers;

use App\Models\ItemOrder;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\ApproveOrderRequest;

class OrderController extends Controller
{
    public function createOrder(CreateOrderRequest $request): \Illuminate\Http\JsonResponse
    {
        $order = Order::query()->create([
            'user_id' => $request->user_id,
            'status' => Order::STATUSES['PENDING'],
        ]);

        foreach ($request->products as $product) {
            ItemOrder::query()->create([
                'order_id' => $order->id,
                'product_id' => $product['id'],
                'price' => Product::query()->find($product['id'])->price,
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
        }

        $order->update([
            'status' => Order::STATUSES['COMPLETED'],
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}
