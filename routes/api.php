<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::namespace('App\Http\Controllers\Api\V1')->prefix('v1')->group( function () {
    
   /*  Users */

    /** Authentication Routes for Users */

    Route::post('/register', 'AuthController@register');
     Route::post('/login', 'AuthController@login');
     Route::post('/forgot', 'AuthController@forgot');
     Route::post('/otp', 'AuthController@checkOTP');
     Route::post('/resend-otp', 'AuthController@resendOTP');
     Route::post('/reset', 'AuthController@reset');

     // Payments
     Route::get('/testmail/{user}', [AuthController::class, 'testmail']);

    Route::middleware(['auth:api'])->group(function () {

        //User
        Route::apiResource('users', UserController::class);
        Route::post('users/photo/{user}', [UserController::class, 'uploadCustomerPhoto']);
        Route::post('users/getuser', [UserController::class, 'getUser']);
        Route::get('/get-user/{id}', 'UserController@show' );
        
    
       

        //Payments
        Route::post('/payment-initialize', 'PaymentController@initializeTransaction' );
        Route::post('payments/reference', 'PaymentChannelController@getPaymentDetails');

        
    });
});

