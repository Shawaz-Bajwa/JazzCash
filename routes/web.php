<?php

use App\Http\Controllers\JazzCashController;
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

Route::get('/', function () {
    return view('jazz.form');
})->name('home');
Route::group(['middleware' => ['web']], function () {
    // Jazz Cash
    Route::post('jazz/payment/initiate', [JazzCashController::class, 'initiatePayment'])->name('jazz.payment.initiate');
    Route::post('/jazz/payment/callback', [JazzCashController::class, 'paymentResponse'])->name('payment.response');
    Route::get('/jazz/success', [JazzCashController::class, 'success'])->name('jazz.success');
    Route::get('/jazz/fail', [JazzCashController::class, 'fail'])->name('jazz.fail');
});
