<?php

namespace App\Implementations\PaymentGateway;

use App\Interfaces\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Opay implements PaymentGateway
{
    public function verify(Request $request)
    {
        $trans_id = $request->reference;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$trans_id/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . env('PAYSTACK_TEST_SK'),
            ),
        ));

        $response1 = curl_exec($curl);

        curl_close($curl);
        return json_decode($response1, true);
    }

    public function pay($postDetails)
    {
        $curl = curl_init();
        // consuming flutterwave endpoint
        $options = [
            CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postDetails),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . env('PAYSTACK_TEST_SK'),
            ],
        ];

        curl_setopt_array($curl, $options);
        $curl_exec = curl_exec($curl);
        curl_close($curl);

        $response = $curl_exec;
        return json_decode($response, true);
    }

    public function webhook(Request $request)
    {
        $response_status = $request->all();
        Log::info('Web Hook Response: ' . json_encode($request->all()));
        Log::info('ID: ' . $response_status['data']['id']);
        Log::info('Status: ' . $response_status['data']['status']);
        Log::info('Payment Reference Id: ' . $response_status['data']['reference']);
        Log::info('Amount: ' . $response_status['data']['amount']);
        Log::info('platform: ' . $response_status['data']['metadata']['posting']);
        
        
        $email = $response_status['data']['customer']['email'];
        $amount = $response_status['data']['amount'] ?? null;
         $reference = $response_status['data']['reference'];
         $meta = $response_status['data']['metadata']?? null;
         $date = $response_status['data']['paid_at']?? null;

        $details = [
            "email" => $email,
            "amount" =>  $amount,
            "reference" => $reference,
            "date" => $date,
            "meta" => $meta,
            

           
        ];

        app('App\Http\Controllers\Api\V1\WalletController')->topUpWallet($details);
        return http_response_code(200);

    }

    public function authorize($postDetails)
    {
        $curl = curl_init();
        // consuming flutterwave endpoint
        $options = [
            CURLOPT_URL => "https://api.paystack.co/transaction/charge_authorization",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postDetails),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . env('PAYSTACK_TEST_SK'),
            ],
        ];

        curl_setopt_array($curl, $options);
        $curl_exec = curl_exec($curl);
        curl_close($curl);

        $response = $curl_exec;
        return json_decode($response, true);
    }

    

}
