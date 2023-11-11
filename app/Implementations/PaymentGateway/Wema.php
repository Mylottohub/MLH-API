<?php

namespace App\Implementations\PaymentGateway;

use App\Interfaces\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class Wema implements PaymentGateway
{
    public function verify(Request $request)
    {

    }

    public function pay($postDetails)
    {
    
    }


    public function webhook(Request $request)
    {
        

    }

    public function authorize($postDetails)
    {
    }

    public function reserve_account($postDetails)
    {
        
    }

    public function authentication()
        {
                $data = array(
                        "username" =>config("wema.username"),
                        "password" => config("password"),
                );
                $data_string = json_encode($data);
                //echo $data_string;
                $ch = curl_init("https://apps.wemabank.com/WemaAPIService/api/Authentication/authenticate");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        array(
                                'Content-Type: application/json', 'VendorID: Centrik'
                        )
                );
                $result = curl_exec($ch);
                $result = json_decode($result, true);
                $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
                /*echo curl_error($ch);
echo $status_code;
print_r($result);
exit;*/

                if (!empty(@$result['token'])) {
                        return $result['token'];
                } else {
                        return FALSE;
                }
        }

        public function fund_transfer($var)
        {
                $token = $this->authentication();

                $bank_code = $var['bank_code'];
                $des_account = $var['des_account'];
                $acc_name = $var['acc_name'];
                $amount = $var['amount'];
                $ref = date('YmdHis', time());

                $edata = array(
                        "myDestinationBankCode" => "$bank_code",
                        "myDestinationAccountNumber" => "$des_account",
                        "myAccountName" => "$acc_name",
                        "myOriginatorName" => "MyLottoHub",
                        "myNarration" => "Fund Transfer",
                        "myPaymentReference" => "$ref",
                        "myAmount" => "$amount",
                        "sourceAccountNo" => "$this->acc_no"
                );
                $xedata = json_encode($edata);

                //echo $xedata;

                $iv = "#$%#^%KCSWITC945";
                $key = ')KCSWITHC%^$$%@H';
                $cipher = 'AES-128-CBC';

                $ab = openssl_encrypt($xedata, $cipher, $key, 0, $iv);

                $data = array("FundTransferRequest" => "$ab");

                $data_string = json_encode($data);
                //echo $data_string;
                $ch = curl_init("https://apps.wemabank.com/WemaAPIService/api/WMServices/NIPFundTransfer");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        array(
                                'Content-Type: application/json', 'VendorID: Centrik', "Authorization: Bearer $token"
                        )
                );
                $result = curl_exec($ch);
                //$result = json_decode($result, true);
                $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
                echo curl_error($ch);
                echo $status_code;
                print_r($result);
                exit;

                if (!empty(@$result['token'])) {
                        //return $result['token'];
                } else {
                        //return FALSE;
                }
        }

        public function transfer($var)
        {
                $ref = $this->CI->gen->ref_no();
                //$ref = 'mlh_16799229914123';
                $account = $var['account'];
                $amount = $var['amount'];

                //enter ref
                $param['table'] = 'wema_ref';
                $param['field'] = 'id, account, amount, ref';
                $param['value'] = "0, '$account', $amount, '$ref'";
                $this->CI->general_model->insert($param);
                $param = array();

                $edata = array(
                        "referenceNumber" => "$ref",
                        "destinationAccount" => "$account",
                        "amount" => $amount,
                        "holdingPeriod" => 30
                );
                $xedata = json_encode($edata);

                //echo $xedata;
                $ch = curl_init("https://lagos-alat-blueapi.azure-api.net/lottowinners/api/Webhook/Lotto-winners");
                //$ch = curl_init("http://20.54.227.23/lottowinners/api/Webhook/Lotto-winners");

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $xedata);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        array(
                                'Content-Type: application/json', "LottoClientID: {$this->clientId}", "Ocp-Apim-Subscription-Key: {$this->skey}",
                                "Authorization: Basic {$this->bauth}"
                        )
                );
                $result = curl_exec($ch);
                $a = $result;
                $result = json_decode($result, true);
                $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
                /*echo curl_error($ch);
                echo $status_code;
                print_r($result);
                exit;*/
                //echo $a;

                $result['transactionId'] = $ref;
                return $result;
        }

        public function connector()
        {
                $URL = 'https://196.43.215.8/ussddmzlotto/swagger/index.html';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $URL);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //this will not echo curl_exec($ch)
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                //curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        array(
                                'Content-Type: application/json'
                        )
                );
                $result = curl_exec($ch);
                $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
                echo curl_error($ch);
                curl_close($ch);
                echo $status_code;

                $result = json_decode($result, true);
                print_r($result);
        }

}