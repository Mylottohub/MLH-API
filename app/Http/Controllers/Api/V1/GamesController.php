<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Implementations\Operators\GoldenChance;
use App\Implementations\Operators\GreenLotto;
use App\Implementations\Operators\LottoMania;
use App\Implementations\Operators\SetLotto;
use App\Implementations\Operators\Wesco;
use App\Models\User;
use App\Models\PlayActivity;
use App\Models\Operator;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    // Check helpers.php for other game process functions
    protected $soapWrapper;

    public function getGames(Request $request)
    {

        if ($request->operator_type == "lottomania" || $request->operator_type == "ghana_game") {
            $lottomania = new LottoMania();
            return $lottomania->getActiveGames($request);
        }

        if ($request->operator_type == "lotto_nigeria") {

            $set_lotto = new SetLotto();
            return $set_lotto->getActiveGames($request);
        }

        if ($request->operator_type == "golden_chance") {

            $golden_chance = new GoldenChance();
            return $golden_chance->getActiveGames($request);
        }

        if ($request->operator_type == "green_lotto") {

            $green_lotto = new GreenLotto();
            return $green_lotto->getActiveGames($request);
        }

        if ($request->operator_type == "wesco") {

            $wesco = new Wesco();
            return $wesco->getActiveGames($request);
        }

    }

    public function playGames(Request $request)
    {

        $id = $request->userID;
        $user = $this->getUser($id);
        $drawId = $request->drawID;
        $drawDate = $request->drawDate;
        $drawTime = $request->drawTime;
        $amount = $request->amount;
        $line = $request->line;
        $total = (int)$amount * (int)$line;
        $operator_type = $request->operator_type;
        /*
        $play = [];
        $gtype = $request->gtype;
        $betTypeId = $request->betTypeId;
        $isperm = $request->isPerm;
        $request->ball;

         */

        if ($user->wallet < $total) {
            return response()->json([
                "code" => "101",
                "msg" => "Insufficient Funds",
            ], 200);
        }

        if (time() >= strtotime($drawTime) && !in_array($operator_type, ['lottomania', 'ghana_game'])) {
            return response()->json([
                "code" => "101",
                "msg" => "Game Closed",
            ], 200);

        }

        $data_parameters = $this->getGameParameters($request);

        if ($operator_type == "lottomania" || $operator_type == "ghana_game") {
            $lottomania = new LottoMania();
            $playResult = $lottomania->playGame($data_parameters, $request);
            return response()->json([
                "gameresult" => $playResult,
           
            ], 200);
        }

        if ($operator_type == "lotto_nigeria") {

            $set_lotto = new SetLotto();
            $playResult = $set_lotto->playGame($data_parameters, $request);
            return response()->json([
                "gameresult" => $playResult,
           
            ], 200);
        }

        if ($operator_type == "golden_chance") {

            $golden_chance = new GoldenChance();
            $playResult = $golden_chance->playGame($data_parameters, $request);
        }

        if ($operator_type == "green_lotto") {

            $green_lotto = new GreenLotto();
            $playResult = $green_lotto->playGame($data_parameters, $request);
            return response()->json([
                "gameresult" => $playResult,
           
            ], 200);
        }
        if ($operator_type== "wesco") {

            $wesco = new Wesco();
            $playResult = $wesco->playGame($data_parameters, $request);
            return response()->json([
                "gameresult" => $playResult,
           
            ], 200);
        }

      
        if (($playResult['status'] != 0 || !isset($playResult['status'])) && $operator_type == 'lotto_nigeria') {

            return response()->json([
                "code" => "101",
                "msg" => $playResult['statusDescription'],
            ], 200);
        } elseif ($playResult['status'] != 200 && ($operator_type == 'lottomania' || $operator_type == 'ghana_game')) {

            return response()->json([
                "code" => "101",
                "msg" => $playResult['msg'],
            ], 200);

        } elseif (($playResult['Result']['status'] != 0 || !isset($playResult['Result']['status'])) && $operator_type == 'green_lotto') {

            return response()->json([
                "code" => "101",
                "msg" => $playResult['Result']['description'],
            ], 200);
        } elseif (($playResult['Result']['status'] != 0 || !isset($playResult['Result']['status'])) && $operator_type == 'wesco') {

            return response()->json([
                "code" => "101",
                "msg" => $playResult['Result']['description'],
            ], 200);
        } else {
            if ($operator_type == 'ghana_game') {
                $xoperator_type = '590_game';
            } else {
                $xoperator_type = $operator_type;
            }
            
        
        }

        if ($playResult['Result']['status'] == 0 ){
            $this->deductWallet($user, $total);

            if ($operator_type == "green_lotto"){
                $green_lotto->save_green_lotto($playResult, $data_parameters, $user );
            }

            if ($operator_type == "lottomania" || $operator_type == "ghana_game"){
                $lottomania->save_lottomania($playResult, $data_parameters, $user );
            }

            if ($operator_type == "lotto_nigeria"){
                $set_lotto->save_lotto_nigeria($playResult, $data_parameters, $user );
            }

            if ($operator_type == "wesco"){
                $wesco->save_wesco($playResult, $data_parameters, $user );
            }
            
        }

        $this->createTransaction($data_parameters, $playResult, $user);
     //   $this->SendSMSUsers($data_parameters, $playResult, $user);
            
    }

    public function saveGameActivity(Request $request){
        $now = date("Y-m-d H:i:s", time());
        $id = $request->userID;
        $operator_type = $request->operator_type;
        $operatorID = Operator::where('name', 'like' , $operator_type)->first()->id;
        $user = $this->getUser($id);
   
       if ($user){
        PlayActivity::create([
            'user' => $user->id,
            'date' => $now,
            'operator'=> $operatorID,
            'game' ?? "",

        ]);

        return response()->json([
           "msg" =>  "Activity Successfully recorded",
        ], 200);
       }else{
        return response()->json([
            "msg" =>  "Problem with adding Play Activity",
         ], 200);
       }


       
       
      

        
    }

    private function getUser($id)
    {
        return User::where('id', $id)->first();
    }

    private function deductWallet($user, $amount, $line)
    {
        $user->wallet -= (int($amount) * int($line)) ;
        $user->save();

    }

    private function getGameParameters(Request $request)
    {
        $trans_id = date('YmdHis', time());
        $ball = $request->ball;
        $amount = (int) $request->amount;
        $total = $request->total;
        $betcost = $amount * 100;
        $line = $request->line;
        $double_chance = $request->double_chance ?: 0;
        $betname = $request->betname;
        $id = $request->userID;
        $user = $this->getUser($id);
        $closetime = $request->closetime;

        if ($request->has('gid')){
            $gid = $request->gid;
        }

        $bettypeid = (int) $request->betTypeId;
        $isperm = $request->isPerm;

        $exp_betname = explode(' ', $betname);
        if ($isperm == 1) {
            $gametype = 'Permutation' . $exp_betname[1];
        } else {
            $lbname = strtolower($exp_betname[1]);
            $xlbname = ucfirst($lbname);
            $gametype = ucfirst($xlbname) . $exp_betname[0];
        }

       // $expball = explode(',', $ball);
       $expball =$ball;
        $expbsize = count($expball);

        $xsesid = session_id();

        $xexpball = array();

        file_put_contents('wescoresult.txt', PHP_EOL . json_encode($xexpball), FILE_APPEND);
        foreach ($expball as $item) {
            $item = (int) $item;
            if ($item < 10) {
                $xexpball[] = '0' . $item;
            } else {
                $xexpball[] = $item;
            }
        }

        $impball = implode(';', $xexpball);

        $param = [
            'trans_id' => $trans_id,
            'ball' => $ball,
            'amount' => $amount,
            'total' => $total,
            'betcost' => $total,
            'line' => $line,
            'double_chance' => $double_chance,
            'betname' => $betname,
            'bettypeid' => $bettypeid,
            'isperm' => $isperm,
            'drawID' => $request->drawID,
            'drawDate' => $request->drawDate,
            'impball' =>$impball,
            'gametype' =>$gametype,
            'ball' => $ball,
            'expbsize' => $expbsize,
            'gid' => $gid ?? "",
            'user' => $user,
            'closetime' =>$closetime,
            'operator_type' => $request->operator_type
        ];
       return $param;
    }

    private function createTransaction($parameters, $playResult, $user){
        $amount = $parameters['amount'];
        $description = "";
        if ($parameters['operator_type'] == 'lotto_nigeria') {
            $channel = 'Set Lotto';
        } elseif ($parameters['operator_type'] == 'green_lotto') {
            $channel = 'Green Lotto';
        } else {
            $channel = '590_game';
        }
        $channel = $channel;
        $now = date("Y-m-d H:i:s", time());
        $abalance = $user->wallet;

        //GL
        $gameIdNumber = $param['gameIdNumber'];
        //BIJ
        $gamePlayId = $param['gamePlayId'];

      /*  if (@$param['user'] != '') {
            $user = $param['user'];
        } else {
            $user = $_SESSION['user'];
        }
        if (@$param['username'] != '') {
            $username = $param['username'];
        } else {
            $username = $this->access->uinfo['username'];
        }*/
        //$ref = trim(com_create_guid(), '{}');
        if ($ref != '') {
            $ref = $parameters['ref'];
        } else {
            $ref = trim($this->getGUID(), '{}');
        }

    /*    if (@$this->access->uinfo['type'] == 'AG' && $type == 'Play') {
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

        Transaction::create([
           
            "amount" => $amount, 
            "date" => $now, 
            "user" => $user->id, 
            "type" => $type, 
            "description" =>$description , 
            "username" =>$username , 
            "channel" => $channel, 
            "ref" => $ref, 
            "abalance" => $abalance, 
            "gameIdNumber" => $gameIdNumber, 
            "gamePlayId" => $gamePlayId, 
            "user_type" => user_type, 
            "customer_tell" => $customer_tell, 
            "commission" =>$ag_com 
        ]);
        
      

        $data['ref'] = $ref;
        $data['tid'] = $this->db->insert_id();
        return $data;
    }

    private function getGUID(){
        mt_srand((float)microtime() * 10000); //optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $uuid = chr(123) // "{"
            . substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12)
            . chr(125); // "}"
        return $uuid;
    }

    private function SendSMSUsers($user){
        $param['tell'] = $user->tell;
        $cus_tell = $this->format_number($param);
        $param['num'] = $cus_tell;
        $param['msg'] = "TICKET INFO \n\n"
            . "Agent Id: {$user->tell} \n"
            //. "Customer Phone: {$_SESSION['agency_tell']} \n"
            . "Operator: {$user->name} \n"
            . "Game Name: $sgame_name \n"
            . "Bet Type: {$_SESSION['gtype']} \n"
            //. "DrawId: $drawID \n"
            . "DrawDate: $xtime \n"
            . "Selection: {$_SESSION['ball']} \n"
            . "Stake: N{$_SESSION['amount']} \n"
            //. "Lines: {$_SESSION['line']} \n"
            //. "Total: N$total_amount \n"
            . "Possible Win: N$max_win \n"
            . "Ticket Id: $ticket_id \n\n"
            //. "Date: {$this->config->item('now')} \n\n"
            . "www.mylottohub.com";
        $this->send_sms($param);
        
    }

    private function addBonus($operator_type, $user){
        $now = date("Y-m-d H:i:s", time());
        switch ($operator_type) {
            case 'lotto_nigeria':
                $lotto_bonus_f = 'sl_bonus';
                $bwallet = 'sl_bwallet';
                break;
            case 'lottomania':
                $lotto_bonus_f = 'lm_bonus';
                $bwallet = 'lm_bwallet';
                break;
            case 'ghana_game':
                $lotto_bonus_f = 'gh_bonus';
                $bwallet = 'gh_bwallet';
                break;
            case 'green_lotto':
                $lotto_bonus_f = 'gl_bonus';
                $bwallet = 'gl_bwallet';
                break;
            case 'wesco':
                $lotto_bonus_f = 'we_bonus';
                $bwallet = 'we_bwallet';
                break;
        }

        if ($user->$lotto_bonus_f > 0) {
            //give lotto commission
            $lotto_bonus = ($user->$lotto_bonus_f * $total_amount) / 100;
            $user->$bwallet += $lotto_bonus;
            $user->save();

            //transaction
            //enter transaction
            $abalance  = $user->$bwallet;
            $amount = $lotto_bonus;
            $type = 'Lotto Bonus';
            $description = "$operator_type Lotto play bonus";
           
        

            Transaction::create([
           
                "amount" => $amount, 
                "date" => $now, 
                "user" => $user->id, 
                "type" => $type, 
                "description" =>$description , 
                "username" =>$user->username , 
                "channel" => $channel ?? "", 
                "ref" => $ref ?? "", 
                "abalance" => $abalance, 
                "gameIdNumber" => $gameIdNumber ?? "", 
                "gamePlayId" => $gamePlayId ?? "", 
                "user_type" => $user_type ?? "", 
                "customer_tell" => $customer_tell ?? "", 
                "commission" =>$ag_com ?? "" 
            ]);
        }
    }

    private function format_number($param)
    {
        $tell = $param['tell'];
        $tell = str_replace('+', '', $tell);
        $len = strlen($tell);
        if ($len == 11) {
            $tell = "234" . substr($tell, 1);
        } elseif ($len == 13) {
            $tell = $tell;
        }
        return $tell;
    }

    private function send_sms($param)
    {
        $num = $param['num'];
        $msg = $param['msg'];

        $senderid = urlencode($this->CI->config->item('senderid'));
        $msg = urlencode($msg);
        $num = urlencode($num);

        $live_url = "https://messaging.updigital-ng.com/smsapi/index?key=46354EF4E32A1A&campaign=0&type=text&contacts=$num&senderid=$senderid&msg=$msg";

        //$live_url = "https://messaging.updigital-ng.com/smsapi/index?key=46354EF4E32A1A&routeid=1&type=text&contacts=$num&senderid=$senderid&msg=$msg";

        //$live_url = "http://www.v2nmobile.com/api/httpsms.php?u=customerservice@expressforecast.com&p=Winners123&m=$msg&r=$num&s=$senderid&t=1";
//$live_url="http://www.daftsms.com/sms_api.php?username=expressforecast&password=expressforecast&sender=$senderid&dest=$num&msg=$msg";
        $parse_url = @file($live_url);
//echo $parse_url[0]."<br />";
//echo $live_url;

    }

    
}
