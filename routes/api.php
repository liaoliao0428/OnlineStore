<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/************************************ backStage API Contorller************************************ */
use App\Http\Controllers\Admin\AdminController;

use App\Http\Controllers\Category\CategoryController;

use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductDetailController;
use App\Http\Controllers\Product\ProductImageController;

use App\Http\Controllers\Order\OrderController;

use App\Http\Controllers\Invoice\InvoiceController;
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
    Route::post('/', [ProductController::class, 'product'])->name('product');
    // 更新
    Route::post('/update', [ProductController::class, 'update'])->name('productUpdate');
    // 商品刪除
    Route::post('/delete', [ProductController::class, 'delete'])->name('productDelete');
    // 上下架
    Route::post('/changeEnable', [ProductController::class, 'changeEnable'])->name('productChangeEnable');
    // 改變排序
    Route::post('/changeSort', [ProductController::class, 'changeSort'])->name('productChangeSort');
    // 上傳圖片
    Route::post('/uploadImage', [ProductController::class, 'uploadImage'])->name('productUploadImage');
});

// 商品子項 productDetail
Route::prefix('productDetail')->group(function () {
    // 取得商品子項
    Route::post('/', [ProductDetailController::class, 'productDetail'])->name('productDetail');
    // 商品子項新增
    Route::post('/insert', [ProductDetailController::class, 'insert'])->name('productDetailInsert');
    // 商品子項更新
    Route::post('/update', [ProductDetailController::class, 'update'])->name('productDetailUpdate');
    // 商品子項刪除
    Route::post('/delete', [ProductDetailController::class, 'delete'])->name('productDetailDelete');
    // 商品子項上下架
    Route::post('/changeEnable', [ProductDetailController::class, 'changeEnable'])->name('productDetailChangeEnable');
    // 改變排序
    Route::post('/changeSort', [ProductDetailController::class, 'changeSort'])->name('productDetailChangeSort');
});

// 商品圖片 productImage
Route::prefix('productImage')->group(function () {
    // 取得商品圖片
    Route::post('/', [ProductImageController::class, 'productImage'])->name('productImage');
    // 商品圖片上傳
    Route::post('/upload', [ProductImageController::class, 'upload'])->name('productImageUpload');
    // 商品圖片刪除
    Route::post('/delete', [ProductImageController::class, 'delete'])->name('productImageDelete');
    // 改變排序
    Route::post('/changeSort', [ProductImageController::class, 'changeSort'])->name('productImageChangeSort');
});
/***************************************product************************************* */

/***************************************invoice************************************* */
// 發票 invoice
Route::prefix('invoice')->group(function () {
    // 查詢字軌使用情況
    Route::any('/', [InvoiceController::class, 'invoice'])->name('invoice');
    // 查詢財政部配號結果
    Route::any('/getinvoiceWordSetting', [InvoiceController::class, 'getinvoiceWordSetting'])->name('getinvoiceWordSetting');
    // 字軌與配號設定
    Route::any('/addInvoice', [InvoiceController::class, 'addInvoice'])->name('addInvoice');
    // 設定字軌號碼狀態
    Route::any('/updateInvoicStatus', [InvoiceController::class, 'updateInvoicStatus'])->name('updateInvoicStatus');

    //excel讀檔案
    Route::post('/import_invoice_excel_csv', [InvoiceController::class, 'import_invoice_excel_csv'])->name('import_invoice_excel_csv');
});
/***************************************invoice************************************* */








