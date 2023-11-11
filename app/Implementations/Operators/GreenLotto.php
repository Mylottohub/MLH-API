<?php

namespace App\Implementations\Operators;

use App\Interfaces\Operator;
use Illuminate\Http\Request;
use App\Models\GreenLottoModel;

class GreenLotto implements Operator
{
    public function getToken(Request $request)
    {

        if (env('APP_ENV') == "local") {

            $lotto_link = config("greenlotto.test");
            $data = array(
                "username" => config("greenlotto.test_username"),
                "password" => config("greenlotto.test_password"),
            );
        } else {

            $lotto_link = config("greenlotto.production");
            $data = array(
                "username" => config("greenlotto.username"),
                "password" => config("greenlotto.password"),
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
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); //get status code
        //echo curl_error($ch);
        //echo $status_code;
        //print_r($result);
        //exit;
        return $result;
    }

    public function getActiveGames(Request $request)
    {
        if (env('APP_ENV') == "local") {

            //$lotto_link = config("greenlotto.test");
            $lotto_link = config("greenlotto.production");
        } else {

            $lotto_link = config("greenlotto.production");
        }

        $time = date("Ymd", time());
        $tomorrow = date("Ymd", time() + 86400);

        $token = $this->getToken($request)['token'];

        $data = array(
            "AgentID" => config("greenlotto.agent_id"),
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

         return $token;

        if ( $result['Result']['status'] == 1){
            return response()->json([

                'result' =>   "Game not avaialable",
            ], 200);
        }

     


        $agame = [];
        $test = "";
        if (isset($result['Result']['draws'][1])) {
            foreach ($result['Result']['draws'] as $item) {
                $dtime = $item['drawdate'];
                $fexdate = date('Y-m-d', strtotime($item['drawdate']));
                $extime = strtotime($fexdate . ' ' . $item['closetime']);
                //echo time()."<br />".$extime."<br />".date('Y-m-d H:i:s', time())."<br />".$fexdate.' '.$item['closetime']."<br /><br />";

                if ($dtime == $time && $extime > time()) {
                    $agame = $item;
                } elseif ($dtime == $tomorrow && $extime > time()) {
                    $agame = $item;
                }
                $test = true;
            }
        }else{
            $dtime = $result['Result']['draws'][0]['drawdate'];
            $fexdate = date('Y-m-d', strtotime($result['Result']['draws'][0]['drawdate']));
            $extime = strtotime($fexdate . ' ' . $result['Result']['draws'][0]['closetime']);
            //echo time()."<br />".$extime."<br />".date('Y-m-d H:i:s', time())."<br />".$fexdate.' '.$item['closetime']."<br /><br />";

            if ($dtime == $time && $extime > time()) {
                $agame = $result['Result']['draws'][0];
            } elseif ($dtime == $tomorrow && $extime > time()) {
                $agame = $result['Result']['draws'][0] ;
            }
            $test = false;
        }

        return response()->json([

            'result' =>   $agame,
        ], 200);

    }

    public function playGame($parameters, Request $request )
    {
        if (env('APP_ENV') == "local") {

            $lotto_link = config("greenlotto.test");
        } else {

            $lotto_link = config("greenlotto.production");
        }
        $token = $this->getToken($request)['token'];
        $total = (int)$parameters['total'];

        $data = array(
            "APIUserID" => config("greenlotto.api_user_id"),
            "Mobile" => config("greenlotto.mobile"),
            "TransID" => $parameters['trans_id'],
            "AgentID" => "99998",
            "GameID" => $parameters['drawID'],
            "DrawDate" => $parameters["drawDate"],
            "Infoarray" => array(array("Betamount" => $total, "Info" => $parameters['impball'], "Gameoption" => "Winning", "Bettypes" => $parameters['gametype']))
        );
        $data_string = json_encode($data);
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

        $result = curl_exec($ch);
        $result = json_decode($result, true);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        //return  $data_string;

        if ($result['Result']['status'] != 0) {
            return $result;
        } else {
            return $result;
        }

    }

    public function getResult(Request $request)
    {

    }

    public function getPrizeList(Request $request)
    {

    }

    public function ticketValidation(Request $request)
    {
        $token = $this->getToken($request)['token'];

        $draw_id = $param['draw_id'];
        $draw_date = date('Ymd', strtotime($param['draw_date']));

        $data = array(
            "APIUserID" => $this->api_user_id,
            "AgentID" => $this->agent_id,
            "Mobileno" => $this->mobile,
            "GameID" => $draw_id,
            "DrawDate" => $draw_date,
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
                'Content-Type: application/json', "Authorization: Bearer $token",
            )
        );

        $result = curl_exec($ch);
        $result = json_decode($result, true);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); //get status code

        /*echo $data_string;
        echo curl_error($ch);
        echo $status_code;
        print_r($result);
        exit;*/

        /*foreach($result['Result']['draws']['info'] as $item)
        {
        //print_r($item)."<br /><br />";
        echo $item['Ticketid'].'<br /><br />';
        }*/

        if ($result['Result']['status'] != 0) {
            return $result;
        } else {
            return $result;
        }
    }

    

    private function save_green_lotto($playResult, $parameters, $user){
       
        $ball = $parameters['ball'];
        $now = date("Y-m-d H:i:s", time());
        $uinfo = $user;
        $username = $user->username;
        $mgametype = $parameters['gametype'];

        //$drawid = $result['Result']['draws']['drawId'];
        $drawid = $parameters['drawid'];
        $drawdate = $playResult['Result']['draws']['drawDate'];
        $drawtime = $playResult['Result']['draws']['drawtime'];
        $drawname = $playResult['Result']['draws']['Drawname'];
        $closetime = $parameters['closetime'];

        $total_amount = $playResult['Result']['draws']['totalamount'];
        $ticket_id = $playResult['Result']['draws']['TikcetId'];
        $transaction_id = $playResult['Result']['draws']['TransactionId'];

        $line = $parameters['line'];
        $amount = $line * (int)$parameters['amount'];
        $stake = $parameters['amount'];

       

        $dtime = date('Y-m-d', strtotime($drawdate)) . ' ' . $drawtime;
        $ctime = date('Y-m-d', strtotime($drawdate)) . ' ' . $closetime;

        /*if ($uinfo['type'] == 'AG') {
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

            GreenLottoModel::create([
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
