<?php

namespace App\Implementations\PaymentGateway;

use App\Interfaces\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Monnify implements PaymentGateway
{
    public function verify(Request $request)
    { /*
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
return json_decode($response1, true);*/
    }

    public function pay($postDetails)
    {
        $api = config("monnify.baseurl") . config("monnify.init-transaction");
        $auth_code = $this->getAccessToken()['responseBody']['accessToken'];
        $curl = curl_init();

        $options = [
            CURLOPT_URL => "$api",
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
                "Authorization: Bearer " . $auth_code,
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

        // Go to the Cards CONTROLLER to save Card
        if (isset($response_status['eventType']) && ($response_status['eventType'] == "SUCCESSFUL_TRANSACTION" || $response_status['eventType'] == "SUCCESSFUL_DISBURSEMENT") || isset($response_status['eventData'])) {

            $transactionReference = $response_status['transactionReference'] ?? null;
            $paymentReference = $response_status['eventData']['paymentReference'];
            $paidOn = $response_status['eventData']['paidOn'] ?? null;
            $metaData = $response_status['eventData']['metaData'] ?? null;
            $amountPaid = $response_status['eventData']['amountPaid'] ?? null;
            $paymentMethod = $response_status['eventData']['paymentMethod'] ?? null;

            $details = [
                "transactionReference" => $transactionReference,
                "reference" => $paymentReference,
                "date" => $paidOn,
                "metaData" => $metaData,
                "amount" => $amountPaid,
                "paymentMethod" => $paymentMethod,
            ];

            app('App\Http\Controllers\Api\V1\WalletController')->topUpWallet($details);

            return http_response_code(200);
        }

    }

    public function authorize($postDetails)
    {$api = config("monnify.baseurl") . config("monnify.disburse");
        $auth_code = $this->getAccessToken()['responseBody']['accessToken'];
        $curl = curl_init();

        $options = [
            CURLOPT_URL => "$api",
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
                "Authorization: Bearer " . $auth_code,
            ],
        ];

        curl_setopt_array($curl, $options);
        $curl_exec = curl_exec($curl);
        curl_close($curl);

        $response = $curl_exec;
        return json_decode($response, true);
    }

    private function getAccessToken()
    {

        $secret_key = env('MONNIFY_SK');
        $api_key = env('MONNIFY_API_KEY');
        $base_64 = base64_encode("$api_key:$secret_key");

        $url = config("monnify.baseurl") . config("monnify.get_token");
        //$request = $client->post($url,  ['body'=>$myBody]);

        $response = Http::withHeaders(['Authorization' => "Basic $base_64", 'Accept' => "application/json"])->post($url);
        return $response;
    }
}