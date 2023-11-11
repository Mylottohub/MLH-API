<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Implementations\PaymentGateway\Paystack;
use App\Implementations\PaymentGateway\Monnify;
use App\Implementations\PaymentGateway\Opay;
use App\Implementations\PaymentGateway\Flutterwave;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Withraw;
use App\Models\ApiAccess;
use App\Http\Requests\CreateUserValidationRequest;
use App\Http\Requests\PaymentValidation;
use Illuminate\Support\Facades\Log;
use App\Mail\AlertWithrawal;
use App\Mail\FundAlert;
use Mail;
use Firebase\JWT\JWT;

class PaymentController extends Controller
{
    public function initializeTransaction(PaymentValidation $request)
    {
        $input = $request->validated();
        $id =  $input['id'] ;
        $user =  $this->getUser($id);
            
       $meta = [

            "user_id" => $user->id,
            "posting" => $input['posting'],

        ];

        if ($request->posting == "monnify"){
            return $this->initializeMonnify($input,$user, $meta);
        }

        if ($request->posting == "paystack"){
            return $this->initializePaystack($input,$user, $meta);
        }

        if ($request->posting == "opay"){
            return $this->initializePaystack($input,$user, $meta);
        }

        if ($request->posting == "flutterwave"){
            return $this->initializeFlutterWave($input,$user, $meta);
        }

      
    }

    public function verifyWebHook(Request $request)
    {
        $response_status = $request->all();
        
        if (isset($response_status['eventType']) && ($response_status['eventType'] == "SUCCESSFUL_TRANSACTION" || $response_status['eventType'] == "SUCCESSFUL_DISBURSEMENT") || isset($response_status['eventData'])){
            $monnify = new Monnify();
            $details = $monnify->webhook($request);
        }

        if(isset($response_status['data']['metadata'])){
            $paystack = new Paystack();
            $details = $paystack->webhook($request);
        }

        if(isset($response_status['data']['event'])){
            $flutter = new Flutterwave();
            $details = $flutter->webhook($request);
        }
          

        return http_response_code(200);

    }

    public function withrawRequest (Request $request)
    {
        $id =  $request->id ;
        $user =  $this->getUser($id);
        $amount = $request->amount;
        $mailData = [
            'name' => $user->name ?? 'user',
            'img'=> asset('storage/logo_mylotto.png'),
            "amount" => $amount
           
            
        ];

        if ($user->wwallet < $amount){
            Mail::to(env('site_email'))->send(new AlertWithrawal($mailData));
            return response()->json(['message' => "Your Balance in your Winning Wallet is insufficienct"]);
        }else{
            $user_withdraw = Withraw::create([
                'user_id' => $user_id,
                'merchant_id' => $merchant_id->merchant_id,
                'activity' => "SMS Tranction from $user_id",
                'date' => Carbon::now(),
                'amount' => $amount,
                'ref' => $ref,
                'status' => ($posting = "Success") ? 'Pending' : 'Success',
                'payment_method' => "Wallet Transaction",
            ]);

            if ($user_withdraw){
                Mail::to(env('site_email'))->send(new AlertWithrawal($mailData));
                return response()->json(['message' => "Request sent successfully"]);
            }else{
                return response()->json(['message' => "There was a problem with the request"]);
            }
            
        }

    }

    private function initializeMonnify($input, User $user, $meta){
      
        $postDetails = [
                "amount" => $input['amount'],
                "customerName" => "$user->firstname $user->lastname",
                "customerEmail" => "$user->email",
                "paymentReference" => $this->unique_code(14),
                "paymentDescription" => "Topup transaction",
                "currencyCode" => "NGN",
                "contractCode" => env('MONNIFY_CONTRACT_CODE'),
                "paymentMethods" => ["CARD", "ACCOUNT_TRANSFER"],
                "metaData" => $meta,
    
            ];
            $monnify = new Monnify();
            $response = $monnify->pay($postDetails);
             
            return  $response;
    }

    private function initializePaystack($input, User $user, $meta){
        $postDetails = [
            // "tx_ref" => $tx_ref,
            "email" => $user->email,
            "amount" =>  $input['amount'] * 100,
            "currency" => "NGN",
            'metadata' => $meta,
            ];

        $paystack = new Paystack();
        $payment = $paystack->pay($postDetails);
        // dd($postDetails);

        if (request()->is('v1/*')) {
            return response()->json(['redirect_url' => $payment['data']['authorization_url']]);
        }
    }

    private function initializeOpay($input, User $user, $meta){
        $postDetails = [
            // "tx_ref" => $tx_ref,
            "email" => $user->email,
            "amount" =>  $input['amount'],
            "currency" => "NGN",
            'metadata' => $meta,
            ];

        $opay = new Opay();
        $payment = $opay->pay($postDetails);
        // dd($postDetails);

        if (request()->is('v1/*')) {
            return response()->json(['redirect_url' => $payment['data']['authorization_url']]);
        }
    }

    private function initializeFlutterWave($input, User $user, $meta){
        $postDetails = [
            "tx_ref" => $this->unique_code(12),
            "amount" =>  $input['amount'],
            "currency" => "NGN",
            'meta' => $meta,
            "customer" =>[
                "email" => $user->email,
                "phonenumber" => $user->tell,
                "name" => $user->name
            ],
            "redirect_url" => " https://sandbox.mylottohub.com"
            ];

        $flutter = new Flutterwave();
        $payment = $flutter->pay($postDetails);
       // return $payment ;

        if (request()->is('v1/*')) {
            return response()->json(['redirect_url' => $payment['data']['link']]);
        }
    }

    private function getUser($id)
    {
        return User::where('id', $id)->first();
    }

    private function unique_code($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }

    
    /*** This Functions are to be consumeed By Opay (OPAP POS and OPAY App),  Tingtel and first Money  */


    public function get_token(Request $request){

        $username = $request->username;
        $password = $request->username;
        $user_code = $request->user_code;

        //check
        $param['table'] = 'api_access';
        $param['and'] = "and username = '$username' and password = '$password' and user_code = $user_code";
        $access_info = ApiAccess::where('username', $username)
                                ->where('password', $password)
                                ->where('user_code', $user_code)
                                ->first();
        if ($access_info == false || access_info == null ) {
            
           return response()->json([
            "status" => 'failed',
           "message" => "User authentication failed"], 409);
        } else {
            $token = array(
                "iss" => env('iss'),
                "aud" => env('aud'),
                "iat" => config("token.iat"),
                "nbf" => config("token.nbf"),
                "exp" =>  config("token.exp"),
                "data" => array(
                    "id" => $access_info['id'],
                    "username" => $access_info['username'],
                    "password" => $access_info['password'],
                    "user_code" => $access_info['user_code']
                )
            );

           
            // generate jwt
            $jwt = JWT::encode($token, env('key'));

            return response()->json([
                "status" => 'successful',
                "message" => "User Authenticated",
                "data" => array("token" => $jwt)], 200);

           
        }

    }

    public function account_lookup(Request $request){
        $token = $request->token;

        $this->validateToken($request);

        $email = $request->user_email;

        //check if user exist
 
        $check_user = User::where('email', $email)->first();

        if ($check_user == FALSE || $check_user == null ) {
        
            // tell the user access denied  & show error message
      
            return response()->json([
                "status" => 'failed',
                "message" => "User does not exist",
                "data" => array("ApiError" => 'INVALID_ACCOUNT')], 401);
        } else {
            $user_name = $check_user->name;
            if ($check_user->name == '') {
                $user_name = $check_user->username;
            }
            
            
            return response()->json([
                "status" => 'successful',
                "message" => "Account lookup successful",
                "data" => array(
                    "user_id" => "{$check_user->id}",
                    "user_name" => "{$user_name}",
                    "user_balance" => (double)$check_user->wallet)], 200);
        }

    }

    public function user_detail(Request $request){

        return $this->account_lookup($request);

    }

    public function place_order(Request $request){
        $token = $request->token;

        $this->validateToken($request);

        $user_id =  $request->user_id;
        $amount =$request->amount;
        $transaction_id = $request->transaction_id;

        if ($amount <= 0) {
      
            // tell the user access denied  & show error message
     
            return response()->json([
                "status" => 'failed',
                "message" => "Transaction amount must be greater than 0",
                "data" => array("ApiError" => 'ZERO_NEGATIVE_TRANSACTION')], 409);
        } elseif (!is_int($amount)) {
        
            // tell the user access denied  & show error message
   
            return response()->json([
                "status" => 'failed',
                "message" => "Amount must be integer",
                "data" => array("ApiError" => 'INTEGER_VALIDATION_ERROR')], 409);
        } else {
//user
           
            $user_info = User::where('id',  $user_id)->first();

//check if transaction id exist
            $trans_exist = 'no';
        
            $ctrans = Transaction::where('ref', $transaction_id)
                                    ->where('channel', 'Tingtel')
                                    ->first();

            if ($ctrans != false || $ctrans != null) {
                $trans_exist = 'yes';
                $tcode = $ctrans->id;
                $bal = $user_info->wallet;
            }

            if ($trans_exist == 'no') {
                $bal = $user_info->wallet + $amount;

                //enter transaction
                      
                $details = [
                    "email" => $user_info->email,
                    "amount" =>  $amount,
                    "reference" => $transaction_id,
                    "date" => date("Y-m-d H:i:s", time()),
                    "channel" => 'Tingtel',
              ];
        
                app('App\Http\Controllers\Api\V1\WalletController')->topUpWallet($details);

                //credit user
         
                $tcode = $int['tid'];

                $eamount = $this->config->item('currency') . number_format($amount, 2);
                //send email
                $mailData = [
                    'name' => $user_info->name ?? 'user',
                    'img'=> asset('storage/logo_mylotto.png'),
                    "amount" => $amount
              
                    
                ];

                Mail::to(env('site_email'))->send(new FundAlert($mailData));
               

// set response code
               
                return response()->json([
                    "status" => 'successful',
                    "message" => "Deposit Successful",
                    "data" => array("request_transaction_id" => "$transaction_id",
                        "result_transaction_id" => "$tcode",
                        "user_id" => "$user_id",
                        "user_balance" => "$bal")], 200);
            } else {
              
                // tell the user access denied  & show error message
             
                return response()->json([
                    "status" => 'failed',
                    "message" => "Duplicate transaction",
                    "data" => array("ApiError" => 'DUPLICATE_TRANSACTION')], 409);
            }
        }

    }

    public function pay_notification(Request $request){
        $token = $request->token;

        $this->validateToken($request);

        $user_id =  $request->user_id;
        $amount =$request->amount;
        $transaction_id = $request->transaction_id;

        if ($amount <= 0) {
      
            // tell the user access denied  & show error message
     
            return response()->json([
                "status" => 'failed',
                "message" => "Transaction amount must be greater than 0",
                "data" => array("ApiError" => 'ZERO_NEGATIVE_TRANSACTION')], 409);
        } elseif (!is_int($amount)) {
        
            // tell the user access denied  & show error message
   
            return response()->json([
                "status" => 'failed',
                "message" => "Amount must be integer",
                "data" => array("ApiError" => 'INTEGER_VALIDATION_ERROR')], 409);
        } else {
//user
           
            $user_info = User::where('id',  $user_id)->first();

//check if transaction id exist
            $trans_exist = 'no';
        
            $ctrans = Transaction::where('ref', $transaction_id)
                                    ->where('channel', 'FirstMonie')
                                    ->first();

            if ($ctrans != false || $ctrans != null) {
                $trans_exist = 'yes';
                $tcode = $ctrans->id;
                $bal = $user_info->wallet;
            }

            if ($trans_exist == 'no') {
                $bal = $user_info->wallet + $amount;

                //enter transaction
                      
                $details = [
                    "email" => $user_info->email,
                    "amount" =>  $amount,
                    "reference" => $transaction_id,
                    "date" => date("Y-m-d H:i:s", time()),
                    "channel" => 'FirstMonie',
              ];
        
                app('App\Http\Controllers\Api\V1\WalletController')->topUpWallet($details);

                //credit user
         
                $tcode = $int['tid'];

                $eamount = $this->config->item('currency') . number_format($amount, 2);
                //send email
                $mailData = [
                    'name' => $user_info->name ?? 'user',
                    'img'=> asset('storage/logo_mylotto.png'),
                    "amount" => $amount
              
                    
                ];

                Mail::to(env('site_email'))->send(new FundAlert($mailData));
               

// set response code
               
                return response()->json([
                    "status" => 'successful',
                    "message" => "Deposit Successful",
                    "data" => array("request_transaction_id" => "$transaction_id",
                        "result_transaction_id" => "$tcode",
                        "user_id" => "$user_id",
                        "user_balance" => "$bal")], 200);
            } else {
              
                // tell the user access denied  & show error message
             
                return response()->json([
                    "status" => 'failed',
                    "message" => "Duplicate transaction",
                    "data" => array("ApiError" => 'DUPLICATE_TRANSACTION')], 409);
            }
        }


    }

    public function query_transaction(){

        $token = $request->token;

        $this->validateToken($request);

        $transaction_id = $request->transaction_id;

         //transaction info
   
         $transaction_info = Transaction::where('ref',$transaction_id )
                                         ->where('channel', 'Opay')
                                         ->orWhere('channel', 'Tingtel')
                                         ->orWhere('channel', 'FirstMonie')
                                         ->first();

         if ($transaction_info == FALSE || $transaction_info ==null ) {

            return response()->json([
                "status" => 'failed',
                "message" => "Transaction does not exist",
               "data" => array("ApiError" => 'INVALID_TRANSACTION')], 409);
                                           
                                        } 
                else {
              
                    //transaction info
                    $user_info = User::where('id', $transaction_info)->first();

                    return response()->json([
                        "status" => 'successful',
                        "message" => "Transaction query successful",
                        "data" => array(
                            "transaction_id" => "{$transaction_info->id}",
                            "amount" => $transaction_info->amount,
                            "user_id" => "{$transaction_info->user}",
                            "msisdn" => "{$user_info->tell}",
                            "type" => "{$transaction_info->type}",
                            "description" => "{$transaction_info->description}",
                            "ref_id" => "{$transaction_info->ref}",
                            "user_balance" => $transaction_info->abalance,
                            "date" => "{$transaction_info->date}",
                            "status" => "Successful"
                        )], 200);
                                         
                                        }

    }

    public function query_transaction_id(){

        $token = $request->token;

        $this->validateToken($request);

        $transaction_id = $request->transaction_id;

         //transaction info
   
         $transaction_info = Transaction::where('ref',$transaction_id )
                                         ->where('channel', 'Opay')
                                         ->first();

         if ($transaction_info == FALSE || $transaction_info ==null ) {

            return response()->json([
                "status" => 'failed',
                "message" => "Transaction does not exist",
               "data" => array("ApiError" => 'INVALID_TRANSACTION')], 409);
                                           
                                        } 
                else {
              
                    //transaction info
                    $user_info = User::where('id', $transaction_info)->first();

                    return response()->json([
                        "status" => 'successful',
                        "message" => "Transaction query successful",
                        "data" => array(
                            "transaction_id" => "{$transaction_info->id}",
                            "amount" => $transaction_info->amount,
                            "user_id" => "{$transaction_info->user}",
                            "msisdn" => "{$user_info->tell}",
                            "type" => "{$transaction_info->type}",
                            "description" => "{$transaction_info->description}",
                            "ref_id" => "{$transaction_info->ref}",
                            "user_balance" => $transaction_info->abalance,
                            "date" => "{$transaction_info->date}",
                            "status" => "Successful"
                        )], 200);
                                         
                                        }

    }

    private function validateToken(Request $request){
        if ($request->has('token')) {
            // if decode succeed, show user details
            try {
                // decode jwt
                $decoded = JWT::decode($token, env('key'), 'HS256');
            } catch (Exception $e) {
                // set response code
               
                // tell the user access denied  & show error message
                return response()->json([
                    "status" => 'failed',
                    "message" => "Player not authenticated",
                    "data" => array("ApiError" => 'NOT_LOGGED_IN')], 401);

               
            }

        } else {

            return response()->json([
                "status" => 'failed',
                "message" => "Player not authenticated",
                "data" => array("ApiError" => 'NOT_LOGGED_IN')], 401);
        }
    }

}