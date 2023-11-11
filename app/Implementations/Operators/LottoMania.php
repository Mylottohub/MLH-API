<?php

namespace App\Implementations\Operators;

use App\Interfaces\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\LottoManiaModel;
use App\Models\GhanaGamesModel;
use Illuminate\Support\Facades\Session;

class LottoMania implements Operator
{
    public function getToken(Request $request)
    {

        $url = config("constant.baseurl") . config("constant.get_merchant") . "/$merchant_id";
        // dd($url);
        $response = Http::withHeaders(['Authorization' => "Bearer $auth_code", 'Accept' => "application/json"])->get($url);
        //dd($response);
        $user = json_decode($response->getBody()->getContents());
    }

    public function getActiveGames(Request $request)
    {

        // $operator_type = $request->operator_type;

        if (env('APP_ENV') == "local") {
        //  $tokenId = config("lottomania.test_token");
        //   $URL = config("lottomania.test") . '/fs/ds/term/gli/1';
           $tokenId = config("lottomania.token");
            $URL = config("lottomania.production") . '/fs/ds/term/gli/1';
        } else {
            $tokenId = config("lottomania.token");
            $URL = config("lottomania.production") . '/fs/ds/term/gli/1';
        }

        if ($request->operator_type == 'lottomania') {
            $gn = 'Indoor 5/90';
        } elseif ($request->operator_type == 'ghana_game') {
            $gn = 'Ghana Game';
        }

        $wtoday = date('w', time());
        $wtoday = $wtoday + 1;
        $no_week = 'N';
        $time = date("Ymd", time());
        $agame = [];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //this will not echo curl_exec($ch)
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        //curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                "tokenId: $tokenId",
            )
        );
        $result = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); //get status code
        //echo curl_error($ch);
        curl_close($ch);
        //echo $status_code;

        $result = json_decode($result, JSON_PRETTY_PRINT);
        return  $result;
     /*
        $dt = date('Y-m-d H:i:s', substr($result['gli'][0]['cgli'][0]['sdt'] , 0, 10));
        $dtime = date("Ymd", strtotime($dt));
        $xdtime = strtotime($dt);
        return response()->json([

            '$dt' =>   $dt ,
            '$dtime' => $dtime,
            '$xdtime' => $xdtime,
            '$wtoday' => date('w', time()) + 1,
            'time' => date("Ymd", time()),
            'my' => date('Y-m-d H:i:s',$result['gli'][0]['cgli'][0]['sdt'])
        
             ], 200);
    */
        if (count($result['gli']) > 0 && isset($result['gli'])) {
            foreach ($result['gli'] as $pitem) {
                if ($pitem['gn']) {
                    if (count($pitem['cgli']) > 0 && isset($pitem['cgli'])) {
                        foreach ($pitem['cgli'] as $item) {
                            $dt = date('Y-m-d H:i:s', substr($item['sdt'], 0, 10));
                            $dtime = date("Ymd", strtotime($dt));
                            $xdtime = strtotime($dt);
                            if (($item['weekDay'] == $wtoday && $time == $dtime && time() < $xdtime) || $no_week == 'Y') {
                                //$agame = $item;
                                array_push($agame, ($item));
                            }
                        }
                    }
                }

            }

            return  count($result['gli']);

            if (countf($agame) > 0) {
                return response()->json([

                    'result' => $agame,
                ], 200);
            }
        }
    }

    public function playGame($parameters, Request $request)

    {
        
        if (env('APP_ENV') == "local") {
            $tokenId = config("lottomania.test_token");
            $URL = config("lottomania.test") ;
        } else {
            $tokenId = config("lottomania.token");
            $URL = config("lottomania.production") ;
        }
        $messageId = $this->ref_no();
        $operator_type = $parameters['operator_type'];
        $token = $tokenId;
        $channel = config("lottomania.channel");

        if ($parameters['isperm'] == 0) {
            switch ($parameters['bettypeid']) {
                case 2:
                    $meg_bet_type = 31;
                    $gmeg_bet_type = 36;
                    break;
                case 3:
                    $meg_bet_type = 32;
                    $gmeg_bet_type = 37;
                    break;
                case 4:
                    $meg_bet_type = 33;
                    $gmeg_bet_type = 38;
                    break;
                case 5:
                    $meg_bet_type = 34;
                    $gmeg_bet_type = 39;
                    break;
            }
        } elseif ($parameters['isperm'] == 1) {
            switch ($parameters['bettypeid']) {
                case 2:
                    $meg_bet_type = 73;
                    $gmeg_bet_type = 77;
                    break;
                case 3:
                    $meg_bet_type = 74;
                    $gmeg_bet_type = 78;
                    break;
                case 4:
                    $meg_bet_type = 75;
                    $gmeg_bet_type = 79;
                    break;
                case 5:
                    $meg_bet_type = 76;
                    $gmeg_bet_type = 80;
                    break;
            }
        }

        if ($parameters['operator_type'] == 'lottomania') {
            $xbtype = $meg_bet_type;
        } elseif ($parameters['operator_type'] == 'ghana_game') {
            $xbtype = $gmeg_bet_type;
        }
        $paf = [
            "messageId" => $messageId,
            "timeStamp" => time(),
            "sessionId" => Session::getId(),
            "playerId" => $parameters['user']->id,
            "tc" =>  $parameters['betcost'],
            "nofP" => 1,
            "pli" => [
                [
                "dc" => $parameters['double_chance'],
                "sli" => $parameters['ball'],
                "did" => 0,
                "pid" => 1,
                "nofcomb" => 1,
                "nofsel" =>$parameters['expbsize'],
                "sa" => $parameters['betcost'],
                "gid" => $parameters['gid'],
                "bid" => $xbtype 
            ]
            ]
        ];


   file_put_contents('lottomaniaresult.txt', print_r($paf, true));
    
        $data = json_encode($paf);
        
    //  return  $data ;


        $ch = curl_init($URL . '/bs/tran/bet');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        $ext_ref_id = config("lottomania.ext_ref_id");
        $pos_id = config("lottomania.pos_id");
        $token = $tokenId;
        $channel = config("lottomania.channel");
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                "Content-Type: application/json",
                "tokenId: $token",
                "channel: $channel"

            )
        );
        //echo $this->token;

        $response = curl_exec($ch);
        $result = json_decode($response, true);
        //echo date("Y-m-d h:m:s", time());
        //echo $this->lotto_link . '/bs/tran/bet';
        /*print_r($result);
        $ak = print_r($result, true);
        echo $ak;*/

        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        //echo curl_error($ch);
        curl_close($ch);
        //echo $status_code;
        
       return $result;
       
        //exit;
        if ($result['status'] != 200) {
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

    }

    private function ref_no(){
        
    $random = 'mlh_'.time() . rand(10*45, 100*98);
    return $random;

    }

    private function save_lottomania ($playResult, $parameters, $user){
        $result = $playResult;
        $ball = $parameters['ball'];
        $now = date("Y-m-d H:i:s", time());
        $uinfo = $user;
        $username = $user->username;
        $GameType = $parameters['gid'];
        $GameTypeName = $parameters['gn'];
        $GameId = $parameters['gid'];
        $GameName = $parameters['gn'];
        $DrawTime = date('Y-m-d H:i:s',  substr($parameters['sdt'], 0, 10));
        $DrawId = $result['pli'][0]['did'];
        $TransId = 'MyLottoHub';
        $TSN = $result['tktNo'];
        $balance = $result['bal'];
        $SessionId = session_id();
        $SelectionType = $parameters['btype'];
        $mgametype = $parameters['gametype'];
        $operator_type = $parameters['operator_type'];
        $double_chance = $parameters['double_chance'] ?: 0;

        $line = $parameters['line'];
        $amount = $line * (int)$parameters['amount'];
        $stake = $parameters['amount'];

     
        $dtime = $DrawTime;
/*
        if ($uinfo['type'] == 'AG') {
            $user_type = 'Agency';
            $ag_com = $param['ag_com'];
            $customer_tell = $_SESSION['agency_tell'];
        } else {
            $user_type = '';
            $ag_com = 0;
            $customer_tell = '';
        }
*/
        $user_type = '';
         $ag_com = 0;
        $customer_tell = '';

        LottoManiaModel::create([
            'num' => $ball, 
            'date' =>$now , 
            'username' => $username,
             'user' => $user->id, 
             'amount' => $amount, 
             'stake' => $stake, 
             'line' => $line, 
             'GameType' => $GameType, 
             'GameTypeName' => $GameTypeName, 
             'GameId' => $GameId, 
             'GameName' => $GameName, 
             'DrawTime' => $DrawTime, 
             'DrawId' => $DrawId, 
             'TranId' => $TransId, 
             'TSN' => $TSN, 
             'SessionId' => $SessionId , 
             'balance' => $balance, 
             'SelectionType' => $SelectionType, 
             'mgametype' => $mgametype, 
             'operator_type' => $operator_type, 
             'double_chance' => $double_chance, 
             'user_type' => $user_type, 
             'customer_tell' => $customer_tel, 
             'commission' =>$ag_com
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
