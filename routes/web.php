<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Resources\ProductCollection;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/catalog', function () {
    return new ProductCollection(Product::paginate());
});
