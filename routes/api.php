<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/************************************ backStage API Contorller************************************ */
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Category\CategoryController;
/************************************ backStage API Contorller************************************ */



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/***************************************category************************************* */
Route::prefix('category')->group(function () {
    //產生作廢發票0501_xml
    Route::post('/all', [App\Http\Controllers\Category\CategoryController::class, 'categoryAll'])->name('categoryAll');
});
/***************************************category************************************* */


/***************************************product************************************* */

// 商品
Route::prefix('product')->group(function () {
    // 取得商品
    Route::post('/', [App\Http\Controllers\Product\ProductController::class, 'product'])->name('product');
    // 更新
    Route::post('/update', [App\Http\Controllers\Product\ProductController::class, 'update'])->name('productUpdate');
    // 商品刪除
    Route::post('/delete', [App\Http\Controllers\Product\ProductController::class, 'delete'])->name('productDelete');
});
/***************************************product************************************* */


