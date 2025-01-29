<?php

namespace App\Http\Controllers;

use App\Models\ItemOrder;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\CreateOrderRequest;

class OrderController extends Controller
{
    public function createOrder(CreateOrderRequest $request)
    {
        $order = Order::query()->create([
            'user_id' => $request->user_id,
            'status' => Order::STATUSES['PENDING'],
        ]);

        foreach ($request->products as $product) {
            $itemOrder = ItemOrder::query()->create([
                'order_id' => $order->id,
                'product_id' => $product['id'],
                'price' => Product::query()->find($product['id'])->price,
            ]);
        }
    }
}
