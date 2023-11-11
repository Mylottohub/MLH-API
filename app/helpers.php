<?php

use App\Implementations\Operators\GreenLotto;
use App\Implementations\Operators\LottoMania;
use App\Implementations\Operators\SetLotto;
use Illuminate\Http\Request;
use App\Models\User;



function line_calc($param)
{
    $n = $param['n'];
    $r = $param['r'];

    if ($n > $r) {
        $rz = '';
        $nz = '';

        for ($x = 1; $x <= $n; $x++) {
            if ($nz == '') {
                $nz = $x;
            } else {
                $nz = $nz * $x;
            }
        }

        for ($y = 1; $y <= $r; $y++) {
            if ($rz == '') {
                $rz = $y;
            } else {
                $rz = $rz * $y;
            }
        }

        $min = $n - $r;
        $minz = '';

        for ($z = 1; $z <= $min; $z++) {
            if ($minz == '') {
                $minz = $z;
            } else {
                $minz = $minz * $z;
            }
        }

        $ans = $nz / ($rz * $minz);
    } else {
        $ans = 1;
    }

    return $ans;
}

function stCheck(Request $request, $line)
{
    switch ($request->operator_type) {
        case 'lotto_nigeria':
            $param['amount'] = $request->amount;
            $param['line'] = $line;
            $param['gtype'] = $request->gtype;
            $lottonigeria = new SetLotto();
            $st_check = $$lottonigeria->stake_limit($param);
            break;
        case 'lottomania':
        case 'ghana_game':
            $param['amount'] = $request->amount;
            $param['line'] = $line;
            $param['btype'] = $request->btype;
            $param['gtype'] = $request->gtype;
            $lottomania = new LottoMania();
            $st_check = $lottomania->stake_limit($param);

            break;
        case 'green_lotto':
            $param['amount'] = $request->amount;
            $param['line'] = $line;
            $param['gtype'] = $request->gtype;
            $green_lotto = new GreenLotto();
            $st_check = $green_lotto->stake_limit($param);

            break;
        case 'wesco':
            $param['amount'] = $request->amount;
            $param['line'] = $line;
            $param['gtype'] = $request->gtype;
            $wesco = new Wesco();
            $st_check = $wesco->stake_limit($param);
            break;
    }

    return $st_check;
}

function yes_no_num($input)
{
    if ($input == 0) {
        $output = 'No';
    } elseif ($input == 1) {
        $output = 'Yes';
    }
    return $output;
}

function maxWinCheck(Request $request, $line)
{
    switch ($request->operator_type) {
        case 'lotto_nigeria':
            $param['gtype'] = $request->gtype;
            $param['amount'] = $request->amount;
            $param['line'] = $line;
            $lottonigeria = new SetLotto();
            $maxQ = $lottonigeria->max_win($param);
            $max_win = $maxQ['max'];

            $gname = $_SESSION['agame'][$_SESSION['game']]['drawAlias'];
            break;
        case 'lottomania':
        case 'ghana_game':
            if ($btype == 6) {
                $max_win = 100 * ($_SESSION['amount'] * $line);
            } elseif ($btype == 5) {
                $param['gtype'] = $gtype;
                $param['amount'] = $_SESSION['amount'];
                $param['line'] = $line;
                $lottomania = new LottoMania();
                $maxQ = $lottomania->max_win($param);
                $max_win = $maxQ['max'];
            }

            $gname = $_SESSION['agame'][$_SESSION['game']]['gn'];
            break;
        case 'green_lotto':
            $param['gtype'] = $gtype;
            $param['amount'] = $_SESSION['amount'];
            $param['line'] = $line;
            $green_lotto = new GreenLotto();
            $maxQ = $green_lotto->max_win($param);
            $max_win = $maxQ['max'];

            $gname = $_SESSION['agame'][$_SESSION['game']]['drawname'];
            break;
        case 'wesco':
            $param['gtype'] = $gtype;
            $param['amount'] = $_SESSION['amount'];
            $param['line'] = $line;
            $wesco = new Wesco();
            $maxQ = $wesco->max_win($param);
            $max_win = $maxQ['max'];

            $gname = $_SESSION['agame'][$_SESSION['game']]['drawname'];
            break;
    }
    
    if (! function_exists('getUser')) {
    function getUser($id)
    {
        return User::where('id', $id)->first();
    }

    }

    function send_subscribers($param)
        {
            $timestamp = time();
            $type = $param['type'];
            $wn1 = @$param['wn1'];
           $wn2 = @$param['wn2'];
           $wn3 = @$param['wn3'];
           $wn4 = @$param['wn4'];
           $wn5 = @$param['wn5'];
           $wn6 = @$param['wn6'];
           $mn1 = @$param['mn1'];
           $mn2 = @$param['mn2'];
           $mn3 = @$param['mn3'];
           $mn4 = @$param['mn4'];
           $mn5 = @$param['mn5'];
           $mn6 = @$param['mn6'];
           $operator = $param['operator'];
           $game = $param['game'];
           
           if($type == 'Result')
           {
               $cmsg = '';
               $winning = $wn1.','.$wn2.','.$wn3.','.$wn4.','.$wn5;
               if($wn6 != '')
               {
                   $winning .= ','.$wn6;
               }
               $cmsg .= "(Winning: $winning)";
               if($mn1 != '' && $mn1 != 0)
               {
                   $machine = $mn1.','.$mn2.','.$mn3.','.$mn4.','.$mn5;
               if($mn6 != '')
               {
                   $machine .= ','.$mn6;
               }
               $cmsg .= "(Machine: $machine)";
               }
               
               $msg = "RESULT: {$this->CI->general->operator_name($operator)} | {$this->CI->general->game_name($game)} => $cmsg";
               //get subscribers
               $param['table'] = 'ussd_subscription';
               $param['field'] = "GROUP_CONCAT(msisdn SEPARATOR ';') xmsisdn";
               $param['and'] = "and operator = $operator and type = 'Result' and $timestamp < expiry_timestamp";
               $param['order'] = "order by date";
               $sub_list = $this->CI->general_model->get_list($param);
               //print_r($sub_list);
               //exit;
               if($sub_list['tresult'][0]['xmsisdn'] != '')
               {
                   //echo $sub_list['tresult'][0]['xmsisdn'];
                   //exit;
                   $exp_msisdn = explode(";", $sub_list['tresult'][0]['xmsisdn']);
                   $chk_msisdn = array_chunk($exp_msisdn, 25);
                   foreach($chk_msisdn as $item)
                   {
                       $in_msisdn = implode(';', $item);
                       //echo $in_msisdn;
                       //exit;
                       //insert
                       $param['table'] = 'sms_job';
                       $param['field'] = "id, recipient, message";
                       $param['value'] = "0, '$in_msisdn', '$msg'";
                       $param['action'] = 'insert';
                       $this->CI->general_model->enter($param);
                       $param = array();
                   }
               }
           }
           elseif($type == 'Forecast')
           {
               $cmsg = '';
               $winning = $wn1;
               if($wn2 != '')
               {
                   $winning .= ','.$wn2;
               }
               if($wn3 != '')
               {
                   $winning .= ','.$wn3;
               }
               if($wn4 != '')
               {
                   $winning .= ','.$wn4;
               }
               if($wn5 != '')
               {
                   $winning .= ','.$wn5;
               }

               $cmsg .= "(Numbers: $winning)";
               
               $msg = "FORECAST: {$this->CI->general->operator_name($operator)} | {$this->CI->general->game_name($game)} => $cmsg";
               //get subscribers
               $param['table'] = 'ussd_subscription';
               $param['field'] = "GROUP_CONCAT(msisdn SEPARATOR ';') xmsisdn";
               $param['and'] = "and operator = $operator and type = 'Forecast' and $timestamp < expiry_timestamp";
               $param['order'] = "order by date";
               $sub_list = $this->CI->general_model->get_list($param);
               if($sub_list['tresult'][0]['xmsisdn'] != '')
               {
                   $exp_msisdn = explode(";", $sub_list['tresult'][0]['xmsisdn']);
                   $chk_msisdn = array_chunk($exp_msisdn, 25);
                   foreach($chk_msisdn as $item)
                   {
                       $in_msisdn = implode(';', $item);
                       //insert
                       $param['table'] = 'sms_job';
                       $param['field'] = "id, recipient, message";
                       $param['value'] = "0, '$in_msisdn', '$msg'";
                       $param['action'] = 'insert';
                       $this->CI->general_model->enter($param);
                       $param = array();
                   }
               }
           }
        }

        function samAlgo($operator, $game, $wn1, $wn2, $wn3, $wn4, $wn5){
            //check sam algo
          
              //update sam algo
    
     
            $sam_num = $wn1 . ',' . $wn2 . ',' . $wn3 . ',' . $wn4 . ',' . $wn5;
            SamAlgo::updateOrCreate(
                [ 'game' => $game, 'operator' => $operator],
                [
                'status' => 'Complete',
                'numresult' => $sam_num,
               
               
               ]);
            
        
        }

        function insertOperatorModel($game_info, $item, $type,  $operator){
            if ($game_info != null) {
                $xg_info = $game_info;
                   
                $game = $xg_info['id'];
                $wnum =  $item['Result'];
                $mnum = $item['MResult'];
                $date =  date("Y-m-d H:i:s", time());
    
                $wexp = explode(',', $wnum);
                $mexp = explode(',', $mnum);
    
                $wn1 = (int)$wexp[0];
                $wn2 = (int)$wexp[1];
                $wn3 = (int)$wexp[2];
                $wn4 = (int)$wexp[3];
                $wn5 = (int)$wexp[4];
                $wn6 = (int)$wexp[5] ? (int)$wexp[5] : 'NULL';
                $mn1 = (int)$mexp[0];
                $mn2 = (int)$mexp[1];
                $mn3 = (int)$mexp[2];
                $mn4 = (int)$mexp[3];
                $mn5 = (int)$mexp[4];
                $mn6 = (int)$mexp[5] ? @(int)$mexp[5] : 'NULL';
                $datetime = $date;
    
                if ($wn6 != 'NULL') {
                    $atw = "-$wn6";
                    $atm = "-$mn6";
                } 
    
                $winning = "$wn1-$wn2-$wn3-$wn4-$wn5" . $atw;
                $machine = "$mn1-$mn2-$mn3-$mn4-$mn5" . $atm;
        
                $dexp = explode(' ', $datetime);
                $ymexp = explode('-', $dexp[0]);
        
                $year = $ymexp[0];
                $month = (int)$ymexp[1];
        
                //$year = date('Y', time());
                //$month = date('n', time());
        
                $wtotal = $wn1 + $wn2 + $wn3 + $wn4 + $wn5;
                $mtotal = $mn1 + $mn2 + $mn3 + $mn4 + $mn5;
                if ($wn6 != 'NULL') {
                    $wtotal = $wtotal + $wn6;
                    $mtotal = $mtotal + $mn6;
                }
    
                $lid =  Results::create([
                    'winning_number' => $winning,
                    'machine_number' => $machine,
                    'game' => $game,
                    'operator' => $operator,
                    'date' => $datetime,
                    'year' => $year,
                    'month' => $month,
                    'winning_total' => $wtotal,
                    'machine_total' => $mtotal,
                    'winning_num1' => $wn1,
                    'winning_num2' => $wn2,
                    'winning_num3' => $wn3,
                    'winning_num4' => $wn4,
                    'winning_num5' => $wn5,
                    'winning_num6' => $wn6 ,
                    'machine_num1' => $mn1,
                    'machine_num2' => $mn2,
                    'machine_num3' => $mn3,
                    'machine_num4' => $mn4,
                    'machine_num5' => $mn5,
                    'machine_num6' => $mn6,
    
                ]);
               
                return $lid;
            }
        }

        function updatePrediction($item, $operator, $game_info){
            $xg_info = $game_info;
                   
            $game = $xg_info['id'];
            $wnum =  $item['Result'];
            $mnum = $item['MResult'];
            $date =  date("Y-m-d H:i:s", time());

            $wexp = explode(',', $wnum);
            $mexp = explode(',', $mnum);

            $wn1 = (int)$wexp[0];
            $wn2 = (int)$wexp[1];
            $wn3 = (int)$wexp[2];
            $wn4 = (int)$wexp[3];
            $wn5 = (int)$wexp[4];
          
            $mn1 = (int)$mexp[0];
            $mn2 = (int)$mexp[1];
            $mn3 = (int)$mexp[2];
            $mn4 = (int)$mexp[3];
            $mn5 = (int)$mexp[4];
           
            $datetime = $date;

            if ($wn6 != 'NULL') {
                $atw = "-$wn6";
                $atm = "-$mn6";
            } 
        }
}
