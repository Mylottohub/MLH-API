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

     /*** This API Endpoints  Are Consumed By Opay (OPAP POS and OPAY App),  Tingtel and first Money  */
     Route::post('/get_token', 'PaymentController@getToken' );
     Route::post('/account_lookup', 'PaymentController@accountLookup' );
     Route::post('/place_order', 'PaymentController@placeOrder' );
     Route::post('/query_transaction', 'PaymentController@initializeTransaction' );
     Route::post('/query_transaction_id', 'PaymentController@initializeTransaction' );
     Route::post('/query_user_detail', 'PaymentController@initializeTransaction' );


     // Games
     Route::post('/get-games', 'GamesController@getGames' );

    Route::middleware(['auth:api'])->group(function () {

        //User
        Route::apiResource('users', UserController::class);
        Route::post('users/photo/{user}', [UserController::class, 'uploadCustomerPhoto']);
        Route::post('users/getuser', [UserController::class, 'getUser']);
        Route::get('/get-user/{id}', 'UserController@show');
        
    
       

        //Payments
        Route::post('/payment-initialize', 'PaymentController@initializeTransaction' );
        Route::post('payments/reference', 'PaymentController@getPaymentDetails');
        Route::post('payments/withdraw-request', 'PaymentController@withrawRequest');

        

         //Wallet
         Route::get('user/wallet/{user_id}', 'WalletController@getWalletBalance' );
         Route::get('user/wallet-bonus/{user_id}', 'WalletController@getWalletBonus');
         Route::get('user/bonus/wallet/{user_id}', 'UserController@getWallets');
 

        //Games
       
        Route::post('/play-games', 'GamesController@playGames' );
        Route::post('/save-play-activity', 'GamesController@saveGameActivity' );
       // Route::post('payments/reference', 'PaymentChannelController@getPaymentDetails');

        
    });
});

