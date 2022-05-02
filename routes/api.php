<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/************************************ backStage API Contorller************************************ */
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Category\CategoryController;

use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\p\Product\ProductDetailController;
use App\Http\Controllers\p\Product\ProductImageController;
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
// 分類 category
Route::prefix('category')->group(function () {
    //產生作廢發票0501_xml
    Route::post('/all', [App\Http\Controllers\Category\CategoryController::class, 'categoryAll'])->name('categoryAll');
});
/***************************************category************************************* */


/***************************************product************************************* */
// 商品 product
Route::prefix('product')->group(function () {
    // 取得商品
    Route::post('/', [App\Http\Controllers\Product\ProductController::class, 'product'])->name('product');
    // 更新
    Route::post('/update', [App\Http\Controllers\Product\ProductController::class, 'update'])->name('productUpdate');
    // 商品刪除
    Route::post('/delete', [App\Http\Controllers\Product\ProductController::class, 'delete'])->name('productDelete');
    // 上下架
    Route::post('/changeEnable', [App\Http\Controllers\Product\ProductController::class, 'changeEnable'])->name('productChangeEnable');
    // 改變排序
    Route::post('/changeSort', [App\Http\Controllers\Product\ProductController::class, 'changeSort'])->name('productChangeSort');
    // 上傳圖片
    Route::post('/uploadImage', [App\Http\Controllers\Product\ProductController::class, 'uploadImage'])->name('productUploadImage');
});

// 商品子項 productDetail
Route::prefix('productDetail')->group(function () {
    // 取得商品子項
    Route::post('/', [App\Http\Controllers\Product\ProductDetailController::class, 'productDetail'])->name('productDetail');
    // 商品子項新增
    Route::post('/insert', [App\Http\Controllers\Product\ProductDetailController::class, 'insert'])->name('productDetailInsert');
    // 商品子項更新
    Route::post('/update', [App\Http\Controllers\Product\ProductDetailController::class, 'update'])->name('productDetailUpdate');
    // 商品子項刪除
    Route::post('/delete', [App\Http\Controllers\Product\ProductDetailController::class, 'delete'])->name('productDetailDelete');
    // 商品子項上下架
    Route::post('/changeEnable', [App\Http\Controllers\Product\ProductDetailController::class, 'changeEnable'])->name('productDetailChangeEnable');
    // 改變排序
    Route::post('/changeSort', [App\Http\Controllers\Product\ProductDetailController::class, 'changeSort'])->name('productDetailChangeSort');
});

// 商品圖片 productImage
Route::prefix('productImage')->group(function () {
    // 取得商品圖片
    Route::post('/', [App\Http\Controllers\Product\ProductImageController::class, 'productImage'])->name('productImage');
    // 商品圖片上傳
    Route::post('/upload', [App\Http\Controllers\Product\ProductImageController::class, 'upload'])->name('productImageUpload');
    // 商品圖片刪除
    Route::post('/delete', [App\Http\Controllers\Product\ProductImageController::class, 'delete'])->name('productImageDelete');
    // 改變排序
    Route::post('/changeSort', [App\Http\Controllers\Product\ProductImageController::class, 'changeSort'])->name('productImageChangeSort');
});
/***************************************product************************************* */


