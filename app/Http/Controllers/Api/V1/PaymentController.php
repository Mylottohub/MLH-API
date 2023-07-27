<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Implementations\PaymentGateway\Paystack;
use App\Implementations\PaymentGateway\Monnify;
use App\Models\User;
use App\Models\Merchant;
use App\Http\Requests\CreateUserValidationRequest;
use App\Http\Requests\PaymentValidation;
use Illuminate\Support\Facades\Log;

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
          

        return http_response_code(200);

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
             

        // return $response;
        if ($response['requestSuccessful']) {
            if (request()->is('api/*')) {
                return response()->json([
                    'status' => "success",
                    'redirect_link' => $response['responseBody']['checkoutUrl'],

                ]);
            }
        }
    }

    private function initializePaystack($input, User $user, $meta){
        $postDetails = [
            // "tx_ref" => $tx_ref,
            "email" => $user->email,
            "amount" =>  $input['amount'],
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

    private function getUser($id)
    {
        return User::where('id', $id)->first();
    }

    private function unique_code($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }


}