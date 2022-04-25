<?php

use Illuminate\Support\Facades\Route;

/************************************ backStage Contorller************************************ */
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Category\CategoryController;
/************************************ backStage Contorller************************************ */

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
    Route::post('/login', [App\Http\Controllers\Admin\AdminController::class, 'login'])->name('adminlogin');

    //首頁
    Route::get('/index', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('adimnIndex');

    //登出
    Route::get('/logout', [App\Http\Controllers\Admin\AdminController::class, 'logout'])->name('adimnLogout');
});

// 商品
Route::prefix('product')->group(function () {
    //首頁
    Route::get('/index/{categoryId}', [App\Http\Controllers\Product\ProductController::class, 'index'])->name('productIndex');
    //新增頁
    Route::get('/add/{categoryId}', [App\Http\Controllers\Product\ProductController::class, 'add'])->name('productAdd');
    // 編輯頁
    Route::get('/edit/{categoryId}', [App\Http\Controllers\Product\ProductController::class, 'edit'])->name('productEdit');
});
