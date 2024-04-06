<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::any('/test/login', function () {
    return view('login');
})
    ->where(['all' => '.*']);

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/payment', [\App\Http\Controllers\PaymentController::class, 'pay'])->name('payment');
Route::match(['GET', 'POST'], 'payment-callback/', [\App\Http\Controllers\PaymentController::class, 'call_back'])->name('payment.callback');
Route::match(['GET', 'POST'], '/callback/redirect', [\App\Http\Controllers\PaymentController::class, 'redirect'])->name('payment.redirect');