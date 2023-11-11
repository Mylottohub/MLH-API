<?php

namespace App\Implementations\Operators;

use App\Interfaces\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use \Carbon\Carbon;

class GoldenChance implements Operator
{
    public function getToken(Request $request){

        date_default_timezone_set('Africa/Lagos');
        $pass = strtoupper(hash('sha512', 'BHSVF1GHT5778HGSHB545'));
        $meg = base64_encode('EXPRESSFORECAST:' . $pass);
//echo $meg."<br />";
//echo $pass."<br />";
        $ch = curl_init("http://testapi.winnersgoldenchance.com/authenticate");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "Authorization: Basic $meg"));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, 'EXPRESSFORECAST' . ":" . "$pass");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: 0'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $rm = json_encode($result);
//echo curl_error($ch);
        curl_close($ch);
//echo $status_code;
//print_r($result);
return $rm;
        $exp = explode('\r\n', $rm);
        $texp = explode('Token: ', $exp[6]);
        $token = trim($texp[1]);

        return $token;
    }

    public function getActiveGames(Request $request){
       // $operator_type = $request->operator_type;
        $token= $this->getToken($request);
        $rtime = date("YmdHis", time());
        $apihash = strtoupper(hash('sha512', "EXPRESSFORECAST4b286cc6-1dcd-4f73-9220-8a7ced0220fe$rtime"));

        if (env('APP_ENV') == "local"){
            
            $URL =  config("goldenchance.test");
        }else{
            $tokenId = config("lottomania.token");
            $URL =  config("goldenchance.production");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Api-Hash-Key: $apihash",
            "Token: $token",
            "Request-Time: $rtime",
            "Api-User-Id: 9",
            "Api-Key: 4b286cc6-1dcd-4f73-9220-8a7ced0220fe",
            "Api-User-Login: EXPRESSFORECAST",
            "Expect:")
        );

//echo "Content-Type: application/json"."<br />"."Api-Hash-Key: $apihash"."<br />"."Token: $token"."<br />"."Request-Time: $rtime"."<br />"."Api-User-Id: 9"."<br />"."Api-Key: 4b286cc6-1dcd-4f73-9220-8a7ced0220fe"."<br />"."Api-User-Login: EXPRESSFORECAST"."<br />";
        $result = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); //get status code
//echo curl_error($ch)."<br />";
        curl_close($ch);
//echo $status_code."<br />";

        $result = json_decode($result);
//print_r($result)."<br />";
//exit;
        return $result;
    }

    public function playGame(Request $request){
        
    }

    public function getResult(Request $request){
        
    }

    public function getPrizeList(Request $request){
        
    }

    public function ticketValidation(Request $request){
        
    }


}