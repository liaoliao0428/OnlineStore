<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/************************************ backStage API Contorller************************************ */
use App\Http\Controllers\Admin\AdminController;

use App\Http\Controllers\Backend\Category\CategoryController;

use App\Http\Controllers\Backend\Product\ProductController;
use App\Http\Controllers\Backend\Product\ProductDetailController;
use App\Http\Controllers\Backend\Product\ProductImageController;

use App\Http\Controllers\Backend\Order\OrderController;

use App\Http\Controllers\Backend\Invoice\InvoiceController;
/************************************ backStage API Contorller************************************ */

/************************************ frontStage API Contorller************************************ */
use App\Http\Controllers\Frontend\Product\ProductApi;

use App\Http\Controllers\Frontend\Category\CategoryApi;

use App\Http\Controllers\Frontend\User\UserApi;

use App\Http\Controllers\Frontend\User\UserMailVerifyApi;

/************************************ frontStage API Contorller************************************ */



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
    Route::post('/all', [CategoryController::class, 'categoryAll'])->name('categoryAll');
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


Route::prefix('frontend')->group(function () {
    // User
    Route::prefix('user')->group(function () {
        // react route 權限驗證
        Route::post('/reactRouteAuthCheck', [UserApi::class, 'reactRouteAuthCheck']);
        // 註冊
        Route::post('/signup', [UserApi::class, 'signup']);
        // 登入
        Route::post('/signin', [UserApi::class, 'signin']);
        // 取得使用者頭像及使用者名稱
        Route::post('/getUserBasicData', [UserApi::class, 'getUserBasicData']);
        // 取得使用者基本資訊
        Route::post('/getUserData', [UserApi::class, 'getUserData']);
        // 更新使用者資料
        Route::patch('/updateUserData', [UserApi::class, 'updateUserData']);
    });

    // UserMailVerify
    Route::prefix('UserMailVerify')->group(function () {
        // 寄驗證碼信件
        Route::post('/sendVerifyMail', [UserMailVerifyApi::class, 'sendVerifyMail']);
    });


    // Category
    Route::prefix('category')->group(function () {
        // 查詢字軌使用情況
        Route::post('/all', [CategoryApi::class, 'categoryAll']);
    });

    // Product
    Route::prefix('product')->group(function () {
        // 搜尋商品
        Route::post('/all', [ProductApi::class, 'productAll']);
        // 商品細項
        Route::post('/detail', [ProductApi::class, 'productDetail']);
    });
    
});







