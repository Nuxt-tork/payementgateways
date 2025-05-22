<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/payment', function () {
    return view('payment.form');
});

Route::post('/payment/checkout', [PaymentController::class, 'initiatePayment'])->name('payment.checkout');
Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
Route::post('/payment/notify', [PaymentController::class, 'paymentNotify'])->name('payment.notify');
Route::get('/payment/fail', [PaymentController::class, 'paymentFail'])->name('payment.fail');
