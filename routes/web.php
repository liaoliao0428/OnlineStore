<?php

use Illuminate\Support\Facades\Route;

/************************************ backStage API Contorller************************************ */
use App\Http\Controllers\Backend\Admin\AdminController;


use App\Http\Controllers\Backend\Product\ProductController;

use App\Http\Controllers\Backend\Order\OrderController;

use App\Http\Controllers\Backend\Invoice\InvoiceController;
/************************************ backStage API Contorller************************************ */

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/************************************ backStage Web Route************************************ */
// 管理者
Route::prefix('admin')->group(function () {
    //後台登入畫面
    Route::get('/login', function () {
        return view('backStage.adminLogin');
    })->name('view.backStage.adminLogin');

    //驗證accessToken
    Route::post('/login', [AdminController::class, 'login'])->name('adminlogin');

    //首頁
    Route::get('/index', [AdminController::class, 'index'])->name('adimnIndex');

    //登出
    Route::get('/logout', [AdminController::class, 'logout'])->name('adimnLogout');
});

// 商品
Route::prefix('product')->group(function () {
    //首頁
    Route::get('/index/{categoryId}', [ProductController::class, 'index'])->name('productIndex');
    //新增頁
    Route::get('/add/{categoryId}', [ProductController::class, 'add'])->name('productAdd');
    // 編輯頁
    Route::get('/edit/{productId}', [ProductController::class, 'edit'])->name('productEdit');
    // 商品新增寫入
    Route::post('/insert', [ProductController::class, 'insert'])->name('productInsert');
});

// 訂單
Route::prefix('order')->group(function () {
    // 首頁
    Route::get('/index', [OrderController::class, 'index'])->name('orderIndex');
    // 詳情
    Route::get('/detail', [OrderController::class, 'detail'])->name('orderDetail');
});

// 發票
Route::prefix('invoice')->group(function () {
    // 首頁
    Route::get('/index', [InvoiceController::class, 'index'])->name('invoiceIndex');
});
