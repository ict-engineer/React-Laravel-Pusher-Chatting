<?php

use Illuminate\Support\Facades\Route;

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

//sendContractInvoiceWeekly
Route::get('/send_weekly_invoice', 'App\Http\Controllers\PaymentController@sendWeeklyInvoice');

//get_paypal_notification
Route::get('/get_paypal_notification', 'App\Http\Controllers\PaymentController@get_paypal_notification');

//paypal_reminder
Route::post('/paypal_reminder', 'App\Http\Controllers\PaymentController@paypal_reminder');

//paypal_payouts
Route::get('/paypal_payouts', 'App\Http\Controllers\PaymentController@paypal_payouts');