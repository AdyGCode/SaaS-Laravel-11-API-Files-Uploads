<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    // only expose the index and show to all users
    Route::apiResource('/products', ProductController::class)
        ->except(['create',]);

    // only allow authenticated users to access create, update and delete
//    Route::apiResource('/products', ProductController::class)
//        ->except(['index','show','create', 'delete',])
//        ->middleware(['auth:sanctum']);

});
