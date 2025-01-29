<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Resources\ProductCollection;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/catalog', function () {
    return new ProductCollection(Product::paginate());
});

Route::post('/create-order', [OrderController::class, 'createOrder']);
Route::put('/approve-order', [OrderController::class, 'approveOrder']);
