<?php

namespace App\Implementations\Operators;

use App\Interfaces\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use \Carbon\Carbon;
use App\Models\WescoModel;

class Wesco implements Operator
{
    public function getToken(Request $request){
        if (env('APP_ENV') == "local") {

            $lotto_link = config("wesco.test");
            $data = array(
                "username" => config("wesco.test_username"),
                "password" => config("wesco.test_password"),
            );
        } else {

            $lotto_link = config("wesco.production");
            $data = array(
                "username" => config("wesco.username"),
                "password" => config("wesco.password"),
            );
        }
       
        $data_string = json_encode($data);
        //echo $data_string;
        $ch = curl_init($lotto_link . '/api-token-auth/');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
            )
        );

        $response = curl_exec($ch);
        $result = json_decode($response, true);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        //get status code
        //echo curl_error($ch);
        //echo $status_code;
        //print_r($result);
        //exit;
        return $result;
    }

    public function getActiveGames(Request $request){
        if (env('APP_ENV') == "local") {

         //   $lotto_link = config("wesco.test");
            $lotto_link = config("wesco.production");
        } else {

            $lotto_link = config("wesco.production");
        }
        
        $time = date("Ymd", time());
        $tomorrow = date("Ymd", time() + 86400);

        $token = $this->getToken($request)['token'];
        

        $data = array(
            "agent_id" => config("wesco.agent_id"),
        );
        $data_string = json_encode($data);
        $url = $lotto_link . '/getgame/';

        //echo $data_string;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json', "Authorization: Bearer $token",
            )
        );

        $response = curl_exec($ch);
        $result = json_decode($response, true);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $agame= array();

       // return $result;
       /*
       $fexdate = date('Y-m-d', strtotime($result['result'][0]['drawdate']));
       return response()->json([

        'drawdate $dtime' =>  $result['result'][0]['drawdate'],
        'close time' => $result['result'][0]['closetime'],
        'formated date $fexdate' => date('Y-m-d', strtotime($result['result'][0]['drawdate'])),
        'formatted time  $extime' => strtotime($fexdate . ' ' . $result['result'][0]['closetime']),
        'time $time' => date("Ymd", time()),
        'time' => time(),
       'tomorrow $tomorrow' => date("Ymd", time() + 86400)
         ], 200);*/

        if ( $result['status'] == 1){
            return response()->json([

                'result' =>   "Game not avaialable",
            ], 200);
        }

        if (isset($result['result'])) {
            for ($x = 0; $x < count($result['result']); $x++){
                $dtime = $result['result'][$x]['drawdate'];
                $fexdate = date('Y-m-d', strtotime($result['result'][$x]['drawdate']));
                $extime = strtotime($fexdate . ' ' . $result['result'][$x]['closetime']);
                //echo time()."<br />".$extime."<br />".date('Y-m-d H:i:s', time())."<br />".$fexdate.' '.$item['closetime']."<br /><br />";

                if ($dtime == $time && $extime > time()) {
                    $agame = $result['result'][$x];
                } elseif ($dtime == $tomorrow && $extime > time()) {
                    $agame = $result['result'][$x];
                }
            }
          /*  foreach ($result['result'] as $item) {
                $dtime = $item['drawdate'];
                $fexdate = date('Y-m-d', strtotime($item['drawdate']));
                $extime = strtotime($fexdate . ' ' . $item['closetime']);
                //echo time()."<br />".$extime."<br />".date('Y-m-d H:i:s', time())."<br />".$fexdate.' '.$item['closetime']."<br /><br />";

                if ($dtime == $time && $extime > time()) {
                    $agame = $item;
                } elseif ($dtime == $tomorrow && $extime > time()) {
                    $agame = $item;
                }
            }*/
            
        }

      //  return $result['result']['result'][0];

        return response()->json([

            'result' =>   $agame,
        ], 200);

        

    }

    public function playGame($parameters, Request $request){

        if (env('APP_ENV') == "local") {

            $lotto_link = config("wesco.test");
          //  $lotto_link = config("wesco.production");
        } else {

            $lotto_link = config("wesco.production");
        }

        $token = $this->getToken($request)['token'];
        
        $data = array(
            "api_user_id" => config("wesco.api_user_id"),
            "agent_id" => config("wesco.agent_id"),
            "mobile_no" => config("wesco.mobile"),
            "transaction_id" => $parameters['trans_id'],
            "game_id" => $parameters['drawID'],
            "drawdate" => $parameters["drawDate"],
            "info_array" => array(array("Betamount" => $parameters['total'], "Info" => $parameters['impball'], "Gameoption" => "Winning", "Bettypes" => $parameters['gametype']))
        );
        $data_string = json_encode($data);
      //  return $data_string;
        file_put_contents( 'wescoresult.txt',  PHP_EOL . $data_string, FILE_APPEND );
        //echo $data_string;
        $ch = curl_init($lotto_link . '/sellticket/');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json', "Authorization: Bearer $token"
            )
        );

        $response = curl_exec($ch);
        $result = json_decode($response, true);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        //echo curl_error($ch);
        //echo $status_code;
        //print_r($result);
        //exit;
        file_put_contents( 'wescoresult.txt',  PHP_EOL . json_encode($result), FILE_APPEND );
        return $result;
       
        if ($result['Result']['status'] != 0) {
            return $result;
        } else {
            return $result;
        }
        
    }

    public function getResult(Request $request){
        
    }

    public function getPrizeList(Request $request){
        
    }

    public function ticketValidation(Request $request){
        
    }

    public function check_winning_tickets($param)
    {
        $token = $param['token'];

        $draw_id = $param['draw_id'];
        $draw_date = date('Ymd', strtotime($param['draw_date']));

        $data = array(
            "api_user_id" => $this->api_user_id,
            "agent_id" => $this->agent_id,
            "mobile_no" => $this->mobile,
            "game_id" => $draw_id,
            "drawdate" => $draw_date
        );
        $data_string = json_encode($data);
        //echo $data_string;
        $ch = curl_init($this->lotto_link . '/winnerlist/');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json', "Authorization: Bearer $token"
            )
        );

        $result = curl_exec($ch);
        $result = json_decode($result, true);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code

        /*echo $data_string;
echo curl_error($ch);
echo $status_code;
print_r($result);*/
//exit;

        /*foreach($result['Result']['draws']['info'] as $item)
{
    //print_r($item)."<br /><br />";
    echo $item['Ticketid'].'<br /><br />';
}*/
/*$j = '{
    "status": 0,
    "description": "Success",
    "generator_datetime": "~20230203 14:49:48",
    "draws": {
        "sale_date": "20230203",
        "sale_time": "10:33:11",
        "info": [
            {
                "ticketid": "7033230343792622",
                "amount": "24000"
            },
            {
                "ticketid": "7033230343796427",
                "amount": "210000"
            }
        ]
    }
}';
$result = json_decode($j, true);*/

        if ($result['status'] != 0) {
            return $result;
        } else {
            return $result;
        }
    }

    private function save_wesco($playResult, $parameters, $user){
        $result = $playResult;
        $ball = $parameters['ball'];
        $now =  date("Y-m-d H:i:s", time());
        $uinfo = $user;
        $username = $user->username;
        $mgametype = $parameters['gametype'];

        //$drawid = $result['Result']['draws']['drawId'];
        $drawid = $parameters['drawid'];
        $drawdate = $result['Result']['draws']['drawDate'];
        $drawtime = $result['Result']['draws']['drawtime'];
        $drawname = $result['Result']['draws']['Drawname'];
        $closetime =$parameters['closetime'];

        $total_amount = $result['Result']['draws']['totalamount'];
        $ticket_id = $result['Result']['draws']['TikcetId'];
        $transaction_id = $result['Result']['draws']['TransactionId'];

        $line = $parameters['line'];
        $amount = $line * (int)$parameters['amount'];
        $stake = $parameters['amount'];

      

        $dtime = date('Y-m-d', strtotime($drawdate)) . ' ' . $drawtime;
        $ctime = date('Y-m-d', strtotime($drawdate)) . ' ' . $closetime;

      /*  if ($uinfo['type'] == 'AG') {
            $user_type = 'Agency';
            $ag_com = $param['ag_com'];
            $customer_tell = $_SESSION['agency_tell'];
        } else {
            $user_type = '';
            $ag_com = 0;
            $customer_tell = '';
        }*/

        $user_type = '';
            $ag_com = 0;
            $customer_tell = '';

            
            WescoModel::create([
                'num' => $ball,
                'date' => $now,
                'username' => $username,
                 'user' => $user->id, 
                 'amount' => $amount, 
                 'stake' => $stake, 
                 'line' => $line, 
                 'mgametype' => $mgametype,
                 'drawname' => $drawname, 
                 'drawdate' => $dtime, 
                 'drawid' => $drawid, 
                 'closetime' => $ctime, 
                 'transaction_id' => $transaction_id, 
                 'totalamount' =>$total_amount ,
                 'TikcetId' => $ticket_id, 
                 'user_type' => $user_type, 
                 'customer_tell' =>$customer_tell, 
                 'commission' =>$ag_com ?? 0
            ]);
       
    }

    private function max_win($param)
    {
        $gtype = $param['gtype'];
        $amount = $param['amount'];
        $line = $param['line'];

        switch ($gtype) {
            case '2 DIRECT':
            case 'PERM 2':
                $wamount = 240;
                break;
            case '3 DIRECT':
            case 'PERM 3':
                $wamount = 2100;
                break;
            case '4 DIRECT':
            case 'PERM 4':
                $wamount = 6000;
                break;
            case '5 DIRECT':
            case 'PERM 5':
                $wamount = 44000;
                break;
        }

        $data['max'] = ($line * $amount) * $wamount;
        return $data;
    }

    
}