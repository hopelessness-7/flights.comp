<?php

use App\Http\Controllers\API\User\AirportSearchController;
use App\Http\Controllers\API\User\FlightController;
use App\Http\Controllers\API\User\BookingController;
use App\Http\Controllers\API\YooKassa\PaymantController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


//Group Route to User - auth, info, verify

Route::post('/login',[LoginController::class, 'login']);
Route::post('/register',[RegisterController::class, 'register']);

Route::get('/user', [UserController::class, 'getUserInfo']);
Route::get('/user/bookings', [UserController::class, 'getUserBookingInfo']);
Route::get('/user/logout', [UserController::class, 'logout']);
Route::get('/user/refresh', [UserController::class, 'refresh']);


// Payment Yookassa

Route::match(['get', 'post'], '/payments/callback', [PaymantController::class, 'callback'])->name('payments.callback');
Route::post('/payments/store', [PaymantController::class, 'store'])->name('payments.store');
Route::get('/payments/{payment_id}/cancel', [PaymantController::class, 'cancellation']);


// Verify email
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['auth:api', 'throttle:6,1'])
    ->name('verification.verify');

// Resend link to verify email
Route::post('/email/verify/resend', [VerificationController::class, 'resend'])->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');


/*
|--------------------------------------------------------------------------
*/

Route::post('/searchAirport', [AirportSearchController::class, 'search']);
Route::get('/flights', [FlightController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::get('/bookings/{code}', [BookingController::class, 'showBooking'])->where('code', '[0-9A-Z]{5}');
Route::patch('/bookings/{code}/seat', [BookingController::class, 'chooseSeat'])->where('code', '[0-9A-Z]{5}'); // validate param
Route::post('/bookings/{code}/delete',[BookingController::class, 'softDeleteBooking']);
