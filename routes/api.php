<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/************************************ backStage API Contorller************************************ */
use App\Http\Controllers\Admin\AdminController;
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

// Route::prefix('admin')->group(function () {
//     //產生作廢發票0501_xml
//     Route::post('/login', [App\Http\Controllers\Admin\AdminController::class, 'generate_C0501_xml_data'])->name('generate_C0501_xml_data');
// });

// Route::middleware(['api', 'cors'])->prefix('admin')->group(function () {
//     //產生作廢發票0501_xml
//     Route::post('/login', [App\Http\Controllers\Admin\AdminController::class, 'generate_C0501_xml_data'])->name('generate_C0501_xml_data');
// });

// Route::group([
//     'middleware' => ['api', 'cors'],
//     'prefix' => 'api',
// ], function ($router) {
//      // some code here
// });

