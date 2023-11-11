<?php

namespace App\Implementations\Operators;

use App\Interfaces\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\SetLottoModel;
use RicorocksDigitalAgency\Soap\Facades\Soap;
require_once base_path().'/vendor/econea/nusoap/src/nusoap.php';

class SetLotto implements Operator
{

    public function getToken(Request $request)
    {

    }

    public function getActiveGames(Request $request)
    {
        $time = date("Ymd", time());
        $tomorrow = date("Ymd", time() + 86400);
        $sevenD = time() + 604800;

       

        if (env('APP_ENV') == "local") {

          /*  $lotto_link = config("lotto_nigeria.test");
            $username = config("lotto_nigeria.test_username");
            $password = config("lotto_nigeria.test_password");
            $terminalID = config("lotto_nigeria.test_terminal_id");
            $operatorID = config("lotto_nigeria.test_operator_id");*/

            $lotto_link = config("lotto_nigeria.production");
            $username = config("lotto_nigeria.username");
            $password = config("lotto_nigeria.password");
            $terminalID = config("lotto_nigeria.terminal_id");
            $operatorID = config("lotto_nigeria.operator_id");
        } else {

            $lotto_link = config("lotto_nigeria.production");
            $username = config("lotto_nigeria.username");
            $password = config("lotto_nigeria.password");
            $terminalID = config("lotto_nigeria.terminal_id");
            $operatorID = config("lotto_nigeria.operator_id");
        } 

     

       
        $c =new \nusoap_client($lotto_link);
//$c->setHTTPProxy("http://178.253.196.22",8070,"{$this->username}","{$this->password}");
//$client->use_curl = TRUE;
$result = $c->call('getGame5Of90Info', array('username' => $username, 'password' => $password, 'terminalId' => $terminalID));

//echo $c->getError();
//print_r($result);
//print_r($c);
//exit;

        if ($c->fault) {
            $error = 'Something went wrong. Try again later';
            return $error;
        } else {
            // check result
            $err_msg = $c->getError();
            if ($err_msg) {
                return $err_msg;
            } else {
            /*    return response()->json([

                    'result' =>  $result,
                ], 200);*/
        
                //print_r($result);
                //var_dump($result);
                //print_r($result['draws']['DrawWS']);
                $agame = array();
              
          //  return $result;

            if ( $result['draws'] == null){
                return response()->json([
    
                    'result' =>   "Game not avaialable",
                ], 200);
            }
                if (isset($result['draws']['DrawWS'][1])) {
                    for ($x=0 ; $x < count($result['draws']['DrawWS']) ; $x++) {
                        //echo $item['drawDate'];
                        
                        $exp_time = explode(' ', $result['draws']['DrawWS'][$x]['drawDate']);
                        
                        $exp_date = explode('/', $exp_time[0]);
                        $edate = $exp_date[2] . '-' . $exp_date[1] . '-' . $exp_date[0];
                        $etime = $exp_time[1] . ':00';

                        $dtime = str_replace('-', '', $edate);
                        //echo $dtime.' | '.$time;
                        $tstime = strtotime($edate . ' ' . $etime);

                        if ($dtime == $time) {
                            $agame[] = $result['draws']['DrawWS'][$x];
                        } elseif ($dtime == $tomorrow) {
                            $agame[] =$result['draws']['DrawWS'][$x];
                        } else {
                            break;
                        }
                    }
                    
                }
                else  {
                    $exp_time = explode(' ', $result['draws']['DrawWS']['drawDate']);
                    
                    $exp_date = explode('/', $exp_time[0]);
                    $edate = $exp_date[2] . '-' . $exp_date[1] . '-' . $exp_date[0];
                    $etime = $exp_time[1] . ':00';

                    $dtime = str_replace('-', '', $edate);
                    //echo $dtime.' | '.$time;
                    $tstime = strtotime($edate . ' ' . $etime);

                    if ($dtime == $time) {
                        $agame[] =  $result['draws']['DrawWS'];
                    } else if ($dtime == $tomorrow) {
                        $agame[] =  $result['draws']['DrawWS'];
                    } else{
                        
                    }
                }
            }
        }
    
        return response()->json([

            'result' =>  $agame,
        ], 200);

        

    }

    public function playGame($parameters, Request $request)
    {
        if (env('APP_ENV') == "local") {

              $lotto_link = config("lotto_nigeria.test");
              $username = config("lotto_nigeria.test_username");
              $password = config("lotto_nigeria.test_password");
              $terminalID = config("lotto_nigeria.test_terminal_id");
              $operatorID = config("lotto_nigeria.test_operator_id");
            /*  $lotto_link = config("lotto_nigeria.production");
              $username = config("lotto_nigeria.username");
              $password = config("lotto_nigeria.password");
              $terminalID = config("lotto_nigeria.terminal_id");
              $operatorID = config("lotto_nigeria.operator_id");*/
  
          } else {
  
              $lotto_link = config("lotto_nigeria.production");
              $username = config("lotto_nigeria.username");
              $password = config("lotto_nigeria.password");
              $terminalID = config("lotto_nigeria.terminal_id");
              $operatorID = config("lotto_nigeria.operator_id");
          } 
        $play = $param['play'];
        $ball = $parameters['ball'];
        $amount = (int) $parameters['amount'];
        $total = $parameters['total'];
        /*$betcost = $amount * 100;
        $line = $param['line'];*/

        $bettypeid = (int) $parameters['bettypeid'];
        $isperm = $parameters['isperm'];

        $expball = explode(',', $ball);
        $expbsize = sizeof($expball);

        $xsesid = Session::getId();

        if ($isperm == 1) {
            $method = 'sell5Of90PermutationTicket';
        } else {
            $method = 'sell5Of90DirectTicket';
        }

        $trans_id = (int) $parameters['trans_id'];

        $drawId = array((int) $parameters['drawID']);
        $parameters['drawId'] = (int) $parameters['drawID'];
        $bets = array("betAmount" => $total, "betNumbers" => $ball, "playType" => $bettypeid);

        $xml = "<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/'
xmlns:lot='http://www.nigerianlotteries.com/services/lotto'
xmlns:lot1='http://lotto.services.server.nsl.nigerianlotteries.com'>
<soapenv:Header/>
<soapenv:Body>
<lot:{$method}>
<lot:in0>{$username}</lot:in0>
<lot:in1>{$password}</lot:in1>
<lot:in2>{$terminal_id}</lot:in2>
<lot:in3>{$operator_id}</lot:in3>
<lot:in4>{$phone_no}</lot:in4>
<lot:in5>{$trans_id}</lot:in5>
<lot:in6>
<!--Zero or more repetitions:-->
<lot:int>{$parameters['drawId']}</lot:int>
</lot:in6>
<lot:in7>
<!--Zero or more repetitions:-->
<lot1:Bets>
<!--Optional:-->
<lot1:betAmount>{$total}</lot1:betAmount>
<!--Optional:-->
<lot1:betNumbers>{$ball}</lot1:betNumbers>
<!--Optional:-->
<lot1:playType>{$bettypeid}</lot1:playType>
<!--Optional:-->
<lot1:quickPick>false</lot1:quickPick>
</lot1:Bets>
</lot:in7>
</lot:{$method}>
</soapenv:Body>
</soapenv:Envelope>";
/*echo $isperm;
$xml = str_replace('<', '&lt;', $xml);
$xml = str_replace('>', '&gt;', $xml);
echo $xml;
exit;*/
        //print_r($drawId);
        //print_r($bets);
        require_once 'plugin/nusoap/src/nusoap.php';
        $c =new \nusoap_client($lotto_link);
        $c->soap_defencoding = 'UTF-8';
        $c->decode_utf8 = false;

//$params = array('username' => $this->username, 'password' => $this->password, 'terminalId' => $this->terminal_id, 'operaterID' => $this->operator_id, 'phoneNumber' => $this->phone_no, 'transactionId' => $trans_id, 'drawIDs' => $drawId, 'Bets' => $bets);
//$result = $c->call($method, $params);

        $result = $c->send($xml, $this->lotto_link);

//var_dump($result);
//print_r($result);
/*echo "<h2>Request</h2>";
echo "<pre>" . htmlspecialchars($c->request, ENT_QUOTES) . "</pre>";
echo "<h2>Response</h2>";
echo "<pre>" . htmlspecialchars($c->response, ENT_QUOTES) . "</pre>";*/
//print_r($c);
//exit;

        if ($c->fault) {
            $error = 'Something went wrong. Try again later';
            return $error;
        } else {
// check result
            $err_msg = $c->getError();
            if ($err_msg) {
                return $err_msg;
            } else {

                return $result['out'];

            }
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

    private function client()
    {
        $opts = array(
            'http' => array(
                'user_agent' => 'PHPSoapClient',
                'Content-Type' => 'text/xml; charset=utf-8',
            ),
        );
        $context = stream_context_create($opts);
        if (env('APP_ENV') == "local") {
            $wsdl = config("lotto_nigeria.test");

            try {
                $this->client = new \SoapClient($wsdl, array(
                    'stream_context' => $context, 'trace' => true,
                    'username' => config("lotto_nigeria.test_username"), 'password' => config("lotto_nigeria.test_password"), 'terminalId' => config("lotto_nigeria.test_terminal_id"))
                );
                return $this->client;
            } catch (\Exception $e) {
                Log::info('Caught Exception in client' . $e->getMessage());
            }
        } else {
            $wsdl = config("lotto_nigeria.production");
            try {
                $this->client = new \SoapClient($wsdl, array(
                    'stream_context' => $context, 'trace' => true,
                    'username' => config("lotto_nigeria.username"), 'password' => config("lotto_nigeria.password"), 'terminalId' => config("lotto_nigeria.terminal_id"))
                );
                return $this->client;
            } catch (\Exception $e) {
                Log::info('Caught Exception in client' . $e->getMessage());
            }
        }

    }

    public function active_games()
    {
       
    }

    public function transaction_id()
    {
        require_once 'plugin/nusoap/src/nusoap.php';
        $c = new nusoap_client($this->lotto_link);
        $result = $c->call('getNextSellTransactionId', array('username' => $this->username, 'password' => $this->password, 'terminalId' => $this->terminal_id));

        if ($c->fault) {
            $error = 'Something went wrong. Try again later';
            return $error;
        } else {
            // check result
            $err_msg = $c->getError();
            if ($err_msg) {
                return $err_msg;
            } else {
                //print_r($result);
                //var_dump($result);
                //echo $result;
                //exit;
                return $result;

            }
        }
    }

    public function play($parameters, Request $request)
    {
        $play = $param['play'];
        $ball = $_SESSION['ball'];
        $amount = (int) $_SESSION['amount'];
        $total = $param['total'];
        /*$betcost = $amount * 100;
        $line = $param['line'];*/

        $bettypeid = (int) $param['bettypeid'];
        $isperm = $param['isperm'];

        $expball = explode(',', $ball);
        $expbsize = sizeof($expball);

        $xsesid = session_id();

        if ($isperm == 1) {
            $method = 'sell5Of90PermutationTicket';
        } else {
            $method = 'sell5Of90DirectTicket';
        }

        $trans_id = (int) $param['transaction_id'];

        $drawId = array((int) $play['drawId']);
        $play['drawId'] = (int) $play['drawId'];
        $bets = array("betAmount" => $total, "betNumbers" => $ball, "playType" => $bettypeid);

        $xml = "<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/'
xmlns:lot='http://www.nigerianlotteries.com/services/lotto'
xmlns:lot1='http://lotto.services.server.nsl.nigerianlotteries.com'>
<soapenv:Header/>
<soapenv:Body>
<lot:{$method}>
<lot:in0>{$this->username}</lot:in0>
<lot:in1>{$this->password}</lot:in1>
<lot:in2>{$this->terminal_id}</lot:in2>
<lot:in3>{$this->operator_id}</lot:in3>
<lot:in4>{$this->phone_no}</lot:in4>
<lot:in5>{$trans_id}</lot:in5>
<lot:in6>
<!--Zero or more repetitions:-->
<lot:int>{$play['drawId']}</lot:int>
</lot:in6>
<lot:in7>
<!--Zero or more repetitions:-->
<lot1:Bets>
<!--Optional:-->
<lot1:betAmount>{$total}</lot1:betAmount>
<!--Optional:-->
<lot1:betNumbers>{$ball}</lot1:betNumbers>
<!--Optional:-->
<lot1:playType>{$bettypeid}</lot1:playType>
<!--Optional:-->
<lot1:quickPick>false</lot1:quickPick>
</lot1:Bets>
</lot:in7>
</lot:{$method}>
</soapenv:Body>
</soapenv:Envelope>";
/*echo $isperm;
$xml = str_replace('<', '&lt;', $xml);
$xml = str_replace('>', '&gt;', $xml);
echo $xml;
exit;*/
        //print_r($drawId);
        //print_r($bets);
        require_once 'plugin/nusoap/src/nusoap.php';
        $c = new nusoap_client($this->lotto_link);
        $c->soap_defencoding = 'UTF-8';
        $c->decode_utf8 = false;

//$params = array('username' => $this->username, 'password' => $this->password, 'terminalId' => $this->terminal_id, 'operaterID' => $this->operator_id, 'phoneNumber' => $this->phone_no, 'transactionId' => $trans_id, 'drawIDs' => $drawId, 'Bets' => $bets);
//$result = $c->call($method, $params);

        $result = $c->send($xml, $this->lotto_link);

//var_dump($result);
//print_r($result);
/*echo "<h2>Request</h2>";
echo "<pre>" . htmlspecialchars($c->request, ENT_QUOTES) . "</pre>";
echo "<h2>Response</h2>";
echo "<pre>" . htmlspecialchars($c->response, ENT_QUOTES) . "</pre>";*/
//print_r($c);
        //exit;

        if ($c->fault) {
            $error = 'Something went wrong. Try again later';
            return $error;
        } else {
            // check result
            $err_msg = $c->getError();
            if ($err_msg) {
                return $err_msg;
            } else {

                return $result['out'];

            }

        }
    }

    private function save_lotto_nigeria($playResult, $parameters, $user){
       
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

            SetLottoModel::create([
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
}
