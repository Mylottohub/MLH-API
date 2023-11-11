<?php

namespace App\Implementations\PaymentGateway;

use App\Interfaces\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MonnifyAccount;

class Monnify implements PaymentGateway
{
    public function verify(Request $request)
    {

    }

    public function pay($postDetails)
    {
     $user = MonnifyAccount::where('user', $postDetails['metaData']['user_id'])->first(); 
     
     if($user){
        return response()->json([

            'bank_name' => "Moniepoint Microfinance Bank",
            "account_name" => "Res",
            "account_number" => $user->accountNumber

        ], 200);
     }else{
       $account= $this->reserve_account($postDetails);
    
       $user_monnify = MonnifyAccount::updateOrCreate(
        [ 'user' =>$postDetails['metaData']['user_id']],
        [
        'contractCode' => $account['contractCode'],
        'accountReference' => $account['accountReference'],
        'accountName' => $account['accounts'][0]['accountName'],
        'currencyCode' => $account['currencyCode'],
        'customerEmail' => $account['customerEmail'],
        'accountNumber' => $account['accounts'][0]['accountNumber'],
        'bankName' => $account['accounts'][0]['bankName'],
        'bankCode' => $account['accounts'][0]['bankCode'],
        'reservationReference' => $account['reservationReference'],
        'status' => $account['status'],
        'createdOn' => $account['createdOn'],
       
       ]);

     }
     if ($user_monnify){
        return response()->json([

            'bank_name' => "Moniepoint Microfinance Bank",
            "account_name" => "Res",
            "account_number" => $user_monnify->accountNumber

        ], 200);
     }else{
        return response()->json([

            'message' => "Problem Creating an Account",
           
        ], 400);
     }
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
            $totalPayable = $response_status['eventData']['totalPayable'];
            $paymentStatus = $response_status['eventData']['paymentStatus'];
            $paymentDescription = $response_status['eventData']['paymentDescription'];
            $customerEmail = $response_status['eventData']['customer']['email'];
            $product_type = $response_status['eventData']['product']['type'];
            $product_ref = $response_status['eventData']['product']['reference'];
            $afet = print_r($response_statuse, true);

            $details = [
                "transactionReference" => $transactionReference,
                "paymentReference" => $paymentReference,
                "paidOn" => $paidOn,
                "metaData" => $metaData,
                "amountPaid" => $amountPaid,
                "paymentMethod" => $paymentMethod,
                "product_type" => $product_type,
                'customerEmail'=>$customerEmail,
                'product_ref' => $product_ref
              
            ];

         if (isset($response_status['eventData']['product']['type']) ) {
                
                app('App\Http\Controllers\WalletController')->topUpWallet($details);
            }

            if ($response_status['eventType'] == "SUCCESSFUL_DISBURSEMENT") {
                $details['reference'] = $response_status['eventData']['reference'] ?? null;
                $details['destinationAccountNumber'] = $response_status['eventData']['destinationAccountNumber'];
                $deatils['destinationBankName'] = $response_status['eventData']['destinationBankName'];
                app('App\Http\Controllers\WalletController')->updateWallet($details);
            }

            if ($response_status['eventType'] == "FAILED_DISBURSEMENT") {
                app('App\Http\Controllers\WalletController')->creditWallet($details);
            }

                       
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
        if (env('APP_ENV') == "local") {

            $secret_key = env('monnify_secret_test_key');
            $api_key = env('monnify_api_test_key');
            $base_64 = base64_encode("$api_key:$secret_key");
    
            $url = config("monnify.baseurl") . config("monnify.get_token");
 
         } else {
 
            $secret_key = env('monnify_secret_key');
            $api_key = env('monnify_api_key');
            $base_64 = base64_encode("$api_key:$secret_key");
    
            $url = config("monnify.monnify_api_link2") . config("monnify.get_token");
         } 

       
        //$request = $client->post($url,  ['body'=>$myBody]);

        $response = Http::withHeaders(['Authorization' => "Basic $base_64", 'Accept' => "application/json"])->post($url);
        return $response;
    }

    public function reserve_account($postDetails)
    {
        $token = $this->getAccessToken()['responseBody']['accessToken'];
        
        $customer_email = $postDetails['customerEmail'];
        $acc_ref = 'MLH_' . time();
        $acc_name = 'Reserve_' . $acc_ref;
        $time = time();
        $contract_code = env('monnify_contract_code') ;

        $details = [
        "accountReference" => $acc_ref,
        "accountName" => $acc_name,
        "currencyCode" => "NGN",
        "contractCode" => $contract_code,
        "customerEmail" => $customer_email,
        "getAllAvailableBanks" => false,
        "preferredBanks" => ["035"],
        ];
  
        $data =json_encode($details);
  
        $url = config("monnify.baseurl") . "/api/v2/bank-transfer/reserved-accounts";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Authorization: Bearer $token")
        );

        $result = curl_exec($ch);
        $result = json_decode($result, true);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); //get status code
/*echo curl_error($ch);
echo $status_code;
print_r($result);
exit;*/
     
        if ($result['responseMessage'] == 'success') {
//echo $result['responseBody'];
            return $result['responseBody'];
        } else {
            return false;
        }

    }

    private function payWeb($postDetails){
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
}