<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GreenLottoFetchID;
use App\Models\Operator;
use App\Models\SamAlgo;
use Illuminate\Support\Facades\DB;

class GreenLottoResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:green-lotto-result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        

        $day = date('N', time());
        $time = date("G:i:s", time());
        $timestamp = '';
        $drawtime = '';

        $drawdate = date('Ymd', time());
        if ($type == 'instant') {

            // <editor-fold defaultstate="collapsed" desc="Instant timestamp">
            if ($time > "7:35:00" && $time < "7:40:00") {
                $drawtime = "7:05:00";
                $timestamp = date('Ymd') . '70500';
            } elseif ($time > "7:45:00" && $time < "7:50:00") {
                $drawtime = "7:15:00";
                $timestamp = date('Ymd') . '71500';
            } elseif ($time > "7:55:00" && $time < "8:00:00") {
                $drawtime = "7:25:00";
                $timestamp = date('Ymd') . '72500';
            } elseif ($time > "8:05:00" && $time < "8:10:00") {
                $drawtime = "7:35:00";
                $timestamp = date('Ymd') . '73500';
            } elseif ($time > "8:15:00" && $time < "8:20:00") {
                $drawtime = "7:45:00";
                $timestamp = date('Ymd') . '74500';
            } elseif ($time > "8:25:00" && $time < "8:30:00") {
                $drawtime = "7:55:00";
                $timestamp = date('Ymd') . '75500';
            } elseif ($time > "8:35:00" && $time < "8:40:00") {
                $drawtime = "8:05:00";
                $timestamp = date('Ymd') . '80500';
            } elseif ($time > "8:45:00" && $time < "8:50:00") {
                $drawtime = "8:15:00";
                $timestamp = date('Ymd') . '81500';
            }             /*elseif ($time > "8:55:00" && $time < "9:00:00")             {
                $drawtime = "8:25:00";
                $timestamp = date('Ymd') . '82500';
            }*/ elseif ($time > "9:05:00" && $time < "9:10:00") {
                $drawtime = "8:35:00";
                $timestamp = date('Ymd') . '83500';
            } elseif ($time > "9:15:00" && $time < "9:20:00") {
                $drawtime = "8:45:00";
                $timestamp = date('Ymd') . '84500';
            } elseif ($time > "9:25:00" && $time < "9:30:00") {
                $drawtime = "8:55:00";
                $timestamp = date('Ymd') . '85500';
            } elseif ($time > "9:35:00" && $time < "9:40:00") {
                $drawtime = "9:05:00";
                $timestamp = date('Ymd') . '90500';
            } elseif ($time > "9:45:00" && $time < "9:50:00") {
                $drawtime = "9:15:00";
                $timestamp = date('Ymd') . '91500';
            }             /*elseif ($time > "9:55:00" && $time < "10:00:00")             {
                $drawtime = "9:25:00";
                $timestamp = date('Ymd') . '92500';
            }*/ elseif ($time > "10:05:00" && $time < "10:10:00") {
                $drawtime = "9:35:00";
                $timestamp = date('Ymd') . '93500';
            } elseif ($time > "10:15:00" && $time < "10:20:00") {
                $drawtime = "9:45:00";
                $timestamp = date('Ymd') . '94500';
            } elseif ($time > "10:25:00" && $time < "10:30:00") {
                $drawtime = "9:55:00";
                $timestamp = date('Ymd') . '95500';
            } elseif ($time > "10:35:00" && $time < "10:40:00") {
                $drawtime = "10:05:00";
                $timestamp = date('Ymd') . '100500';
            } elseif ($time > "10:45:00" && $time < "10:50:00") {
                $drawtime = "10:15:00";
                $timestamp = date('Ymd') . '101500';
            } elseif ($time > "10:55:00" && $time < "11:00:00") {
                $drawtime = "10:25:00";
                $timestamp = date('Ymd') . '102500';
            } elseif ($time > "11:05:00" && $time < "11:10:00") {
                $drawtime = "10:35:00";
                $timestamp = date('Ymd') . '103500';
            } elseif ($time > "11:15:00" && $time < "11:20:00") {
                $drawtime = "10:45:00";
                $timestamp = date('Ymd') . '104500';
            } elseif ($time > "11:25:00" && $time < "11:30:00") {
                $drawtime = "10:55:00";
                $timestamp = date('Ymd') . '105500';
            } elseif ($time > "11:35:00" && $time < "11:40:00") {
                $drawtime = "11:05:00";
                $timestamp = date('Ymd') . '110500';
            } elseif ($time > "11:45:00" && $time < "11:50:00") {
                $drawtime = "11:15:00";
                $timestamp = date('Ymd') . '111500';
            } elseif ($time > "11:55:00" && $time < "12:00:00") {
                $drawtime = "11:25:00";
                $timestamp = date('Ymd') . '112500';
            } elseif ($time > "12:05:00" && $time < "12:10:00") {
                $drawtime = "11:35:00";
                $timestamp = date('Ymd') . '113500';
            } elseif ($time > "12:15:00" && $time < "12:20:00") {
                $drawtime = "11:45:00";
                $timestamp = date('Ymd') . '114500';
            } elseif ($time > "12:25:00" && $time < "12:30:00") {
                $drawtime = "11:55:00";
                $timestamp = date('Ymd') . '115500';
            } elseif ($time > "12:35:00" && $time < "12:40:00") {
                $drawtime = "12:05:00";
                $timestamp = date('Ymd') . '120500';
            } elseif ($time > "12:45:00" && $time < "12:50:00") {
                $drawtime = "12:15:00";
                $timestamp = date('Ymd') . '121500';
            } elseif ($time > "12:55:00" && $time < "13:00:00") {
                $drawtime = "12:25:00";
                $timestamp = date('Ymd') . '122500';
            } elseif ($time > "13:05:00" && $time < "13:10:00") {
                $drawtime = "12:35:00";
                $timestamp = date('Ymd') . '123500';
            } elseif ($time > "13:15:00" && $time < "13:20:00") {
                $drawtime = "12:45:00";
                $timestamp = date('Ymd') . '124500';
            } elseif ($time > "13:25:00" && $time < "13:30:00") {
                $drawtime = "12:55:00";
                $timestamp = date('Ymd') . '125500';
            } elseif ($time > "13:35:00" && $time < "13:40:00") {
                $drawtime = "13:05:00";
                $timestamp = date('Ymd') . '130500';
            } elseif ($time > "13:45:00" && $time < "13:50:00") {
                $drawtime = "1:15:00";
                $timestamp = date('Ymd') . '11500';
            } elseif ($time > "13:55:00" && $time < "14:00:00") {
                $drawtime = "1:25:00";
                $timestamp = date('Ymd') . '12500';
            } elseif ($time > "14:05:00" && $time < "14:10:00") {
                $drawtime = "13:35:00";
                $timestamp = date('Ymd') . '133500';
            } elseif ($time > "14:15:00" && $time < "14:20:00") {
                $drawtime = "1:45:00";
                $timestamp = date('Ymd') . '14500';
            } elseif ($time > "14:25:00" && $time < "14:30:00") {
                $drawtime = "1:55:00";
                $timestamp = date('Ymd') . '15500';
            } elseif ($time > "14:35:00" && $time < "14:40:00") {
                $drawtime = "14:05:00";
                $timestamp = date('Ymd') . '140500';
            } elseif ($time > "14:45:00" && $time < "14:50:00") {
                $drawtime = "2:15:00";
                $timestamp = date('Ymd') . '21500';
            } elseif ($time > "14:55:00" && $time < "15:00:00") {
                $drawtime = "2:25:00";
                $timestamp = date('Ymd') . '22500';
            } elseif ($time > "15:05:00" && $time < "15:10:00") {
                $drawtime = "14:35:00";
                $timestamp = date('Ymd') . '143500';
            } elseif ($time > "15:15:00" && $time < "15:20:00") {
                $drawtime = "2:45:00";
                $timestamp = date('Ymd') . '24500';
            } elseif ($time > "15:25:00" && $time < "15:30:00") {
                $drawtime = "2:55:00";
                $timestamp = date('Ymd') . '25500';
            } elseif ($time > "15:35:00" && $time < "15:40:00") {
                $drawtime = "15:05:00";
                $timestamp = date('Ymd') . '150500';
            } elseif ($time > "15:45:00" && $time < "15:50:00") {
                $drawtime = "3:15:00";
                $timestamp = date('Ymd') . '31500';
            } elseif ($time > "15:55:00" && $time < "16:00:00") {
                $drawtime = "3:25:00";
                $timestamp = date('Ymd') . '32500';
            } elseif ($time > "16:05:00" && $time < "16:10:00") {
                $drawtime = "15:35:00";
                $timestamp = date('Ymd') . '153500';
            } elseif ($time > "16:15:00" && $time < "16:20:00") {
                $drawtime = "3:45:00";
                $timestamp = date('Ymd') . '34500';
            } elseif ($time > "16:25:00" && $time < "16:30:00") {
                $drawtime = "3:55:00";
                $timestamp = date('Ymd') . '35500';
            } elseif ($time > "16:35:00" && $time < "16:40:00") {
                $drawtime = "16:05:00";
                $timestamp = date('Ymd') . '160500';
            } elseif ($time > "16:45:00" && $time < "16:50:00") {
                $drawtime = "4:15:00";
                $timestamp = date('Ymd') . '41500';
            } elseif ($time > "16:55:00" && $time < "17:00:00") {
                $drawtime = "4:25:00";
                $timestamp = date('Ymd') . '42500';
            } elseif ($time > "17:05:00" && $time < "17:10:00") {
                $drawtime = "16:35:00";
                $timestamp = date('Ymd') . '163500';
            } elseif ($time > "17:15:00" && $time < "17:20:00") {
                $drawtime = "4:45:00";
                $timestamp = date('Ymd') . '44500';
            } elseif ($time > "17:25:00" && $time < "17:30:00") {
                $drawtime = "4:55:00";
                $timestamp = date('Ymd') . '45500';
            } elseif ($time > "17:35:00" && $time < "17:40:00") {
                $drawtime = "17:05:00";
                $timestamp = date('Ymd') . '170500';
            } elseif ($time > "17:45:00" && $time < "17:50:00") {
                $drawtime = "5:15:00";
                $timestamp = date('Ymd') . '51500';
            } elseif ($time > "17:55:00" && $time < "18:00:00") {
                $drawtime = "5:25:00";
                $timestamp = date('Ymd') . '52500';
            } elseif ($time > "18:05:00" && $time < "18:10:00") {
                $drawtime = "17:35:00";
                $timestamp = date('Ymd') . '173500';
            } elseif ($time > "18:15:00" && $time < "18:20:00") {
                $drawtime = "5:45:00";
                $timestamp = date('Ymd') . '54500';
            } elseif ($time > "18:25:00" && $time < "18:30:00") {
                $drawtime = "5:55:00";
                $timestamp = date('Ymd') . '55500';
            } elseif ($time > "18:35:00" && $time < "18:40:00") {
                $drawtime = "18:05:00";
                $timestamp = date('Ymd') . '180500';
            } elseif ($time > "18:45:00" && $time < "18:50:00") {
                $drawtime = "6:15:00";
                $timestamp = date('Ymd') . '61500';
            } elseif ($time > "18:55:00" && $time < "19:00:00") {
                $drawtime = "6:25:00";
                $timestamp = date('Ymd') . '62500';
            } elseif ($time > "19:05:00" && $time < "19:10:00") {
                $drawtime = "18:35:00";
                $timestamp = date('Ymd') . '183500';
            } elseif ($time > "19:15:00" && $time < "19:20:00") {
                $drawtime = "6:45:00";
                $timestamp = date('Ymd') . '64500';
            } elseif ($time > "19:25:00" && $time < "19:30:00") {
                $drawtime = "6:55:00";
                $timestamp = date('Ymd') . '65500';
            } elseif ($time > "19:35:00" && $time < "19:40:00") {
                $drawtime = "19:05:00";
                $timestamp = date('Ymd') . '190500';
            } elseif ($time > "19:45:00" && $time < "19:50:00") {
                $drawtime = "7:15:00";
                $timestamp = date('Ymd') . '71500';
            } elseif ($time > "19:55:00" && $time < "20:00:00") {
                $drawtime = "7:25:00";
                $timestamp = date('Ymd') . '72500';
            } elseif ($time > "20:05:00" && $time < "20:10:00") {
                $drawtime = "19:35:00";
                $timestamp = date('Ymd') . '193500';
            } elseif ($time > "20:15:00" && $time < "20:20:00") {
                $drawtime = "7:45:00";
                $timestamp = date('Ymd') . '74500';
            } elseif ($time > "20:25:00" && $time < "20:30:00") {
                $drawtime = "7:55:00";
                $timestamp = date('Ymd') . '75500';
            } elseif ($time > "20:35:00" && $time < "20:40:00") {
                $drawtime = "20:05:00";
                $timestamp = date('Ymd') . '200500';
            } elseif ($time > "20:45:00" && $time < "20:50:00") {
                $drawtime = "8:15:00";
                $timestamp = date('Ymd') . '81500';
            } elseif ($time > "20:55:00" && $time < "21:00:00") {
                $drawtime = "8:25:00";
                $timestamp = date('Ymd') . '82500';
            } elseif ($time > "21:05:00" && $time < "21:10:00") {
                $drawtime = "20:35:00";
                $timestamp = date('Ymd') . '203500';
            } elseif ($time > "21:15:00" && $time < "21:20:00") {
                $drawtime = "8:45:00";
                $timestamp = date('Ymd') . '84500';
            } elseif ($time > "21:25:00" && $time < "21:30:00") {
                $drawtime = "8:55:00";
                $timestamp = date('Ymd') . '85500';
            } elseif ($time > "21:35:00" && $time < "21:40:00") {
                $drawtime = "21:05:00";
                $timestamp = date('Ymd') . '210500';
            } elseif ($time > "21:45:00" && $time < "21:50:00") {
                $drawtime = "9:15:00";
                $timestamp = date('Ymd') . '91500';
            } elseif ($time > "21:55:00" && $time < "22:00:00") {
                $drawtime = "9:25:00";
                $timestamp = date('Ymd') . '92500';
            } elseif ($time > "22:05:00" && $time < "22:10:00") {
                $drawtime = "21:35:00";
                $timestamp = date('Ymd') . '213500';
            } elseif ($time > "22:15:00" && $time < "22:20:00") {
                $drawtime = "9:45:00";
                $timestamp = date('Ymd') . '94500';
            } elseif ($time > "22:25:00" && $time < "22:30:00") {
                $drawtime = "9:55:00";
                $timestamp = date('Ymd') . '95500';
            } elseif ($time > "22:35:00" && $time < "22:40:00") {
                $drawtime = "22:05:00";
                $timestamp = date('Ymd') . '220500';
            } elseif ($time > "22:45:00" && $time < "22:50:00") {
                $drawtime = "10:15:00";
                $timestamp = date('Ymd') . '101500';
            } elseif ($time > "22:55:00" && $time < "23:00:00") {
                $drawtime = "10:25:00";
                $timestamp = date('Ymd') . '102500';
            } elseif ($time > "23:05:00" && $time < "23:10:00") {
                $drawtime = "22:35:00";
                $timestamp = date('Ymd') . '223500';
            } elseif ($time > "23:15:00" && $time < "23:20:00") {
                $drawtime = "10:45:00";
                $timestamp = date('Ymd') . '104500';
            } elseif ($time > "23:25:00" && $time < "23:30:00") {
                $drawtime = "10:55:00";
                $timestamp = date('Ymd') . '105500';
            } // </editor-fold>

            //lotto
        } else {
            if ($time > "9:30:00" && $time < "10:00:00" && $day != '7') {
                //echo '1';
                $drawtime = "9:00:00";
                $timestamp = date('Ymd') . '90000';
            } elseif ($time > "10:30:00" && $time < "12:00:00" && $day != '7') {
                //echo '1';
                $drawtime = "10:00:00";
                $timestamp = date('Ymd') . '100000';
            } elseif ($time > "12:30:00" && $time < "15:00:00") {
                //echo '2';
                $drawtime = "12:00:00";
                $timestamp = date('Ymd') . '120000';
            } elseif ($time > "15:30:00" && $time < "16:00:00") {
                //echo '3';
                $drawtime = "15:00:00";
                $timestamp = date('Ymd') . '150000';
            } elseif ($time > "16:30:00" && $time < "17:00:00") {
                //echo '3';
                $drawtime = "16:00:00";
                $timestamp = date('Ymd') . '160000';
            } elseif ($time > "17:30:00" && $time < "18:00:00") {
                //echo '4';
                $drawtime = "17:00:00";
                $timestamp = date('Ymd') . '170000';
            } elseif ($time > "19:00:00" && $time < "20:25:00") {
                //echo '4';
                $drawtime = "18:30:00";
                $timestamp = date('Ymd') . '183000';
            } elseif ($time > "20:55:00" && $time < "22:00:00" && $day != '7') {
                //echo '1';
                $drawtime = "20:25:00";
                $timestamp = date('Ymd') . '202500';
            } elseif ($time > "22:30:00" && $time < "23:00:00") {
                //echo '4';
                $drawtime = "22:00:00";
                $timestamp = date('Ymd') . '220000';
            } elseif ($time > "23:00:00" && $time < "23:59:00") {
                //echo '5';
                $drawtime = "22:30:00";
                $timestamp = date('Ymd') . '223000';
            }
        }

        if ($timestamp != '' && $drawtime != '') {
            //check if fetch_id exist
            $query = GreenLottoFetchID::where('fetch_id', "$timestamp")->first();
            if ($query != null) {
                $data = array(
                    "ApiKey" => 'testkey123',
                    "Mode" => 2,
                    "SequenceId" => "web_154356498774",
                    "DrawDate" => "$drawdate",
                    "DrawTime" => "$drawtime",
                    "Parms" => "GLRESULT",
                    "Timestamp" => "$timestamp",
                    "apiUserid" => "mylotto",
                    "apiPassword" => "mylott2545*ng"
                );
                $data_string = json_encode($data);
                $ch = "";

                if (env('APP_ENV') == "local") {

                    $ch = curl_init("http://115.110.148.74/GLRESULT/GLResultAPI/GetWebResult");
                } else {
        
                    $ch = curl_init("http://115.110.148.105/GLRESULT/GLResultAPI/GetWebResult");
                }
                //
                
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json'
                    )
                );

                $result = curl_exec($ch);
                $result = json_decode($result, true);
                //print_r($result);
                //var_dump($result);
                //echo $drawdate.' | '.$timestamp.' | '.$drawtime;
                //echo curl_error($ch);
                //exit;

               
                           

                //echo $result['AckResponse']."<br />".$result['AckCode']."<br />".$result['WebResultArray'][0]['GameName']."<br />".$result['WebResultArray'][0]['Result']."<br />".$result['WebResultArray'][0]['MResult'];
                if ($result['AckCode'] == 0) {
                    foreach ($result['WebResultArray'] as $item) {
                        $operator = 43;
                        $gl_name = strtoupper(trim($item['GameName']));
                        //get game info
                        $game_info = Operator::where('id', $operator)
                                               ->where('del', '<>', 'Y')
                                               ->where('name',$gl_name )
                                               ->get();
                        
                    
                      //  $this->db->query("select * from game where operator = $operator and del <> 'Y' and name='$gl_name'");
                       
                    }
                    $type = 'green_lotto';
                    $rid = insertOperatorModel($game_info, $item, $type,  $operator);

                    samAlgo($operator, $game, $wn1, $wn2, $wn3, $wn4, $wn5);



                    //insert green_lotto_fetch_id
                    if (!isset($rid)) {
                        $rid = '1';
                    }
                   
                    GreenLottoFetchID::create([
                        'fetch_id' =>$timestamp,
                        'result' => $rid
                    ]);
                }
            }
        }
    }

    private function insertOperatorModel($game_info){
        if ($game_info != null) {
          

            $rid = $this->operator_model->insert_result($param);
            //update user prediction
            $param['rid'] = $rid;
            if ($type != 'instant') {
                $this->welcome_model->update_prediction($param);
                $this->forecast_model->post_update($param);
                $param = array();
            }

            if ($type != 'instant') {
                //ussd action
                $exp_wnum = explode(',', $item['Result']);
                $exp_mnum = explode(',', $item['MResult']);

                $param['type'] = 'Result';
                $param['wn1'] = @$exp_wnum[0];
                $param['wn2'] = @$exp_wnum[1];
                $param['wn3'] = @$exp_wnum[2];
                $param['wn4'] = @$exp_wnum[3];
                $param['wn5'] = @$exp_wnum[4];
                $param['wn6'] = @$exp_wnum[5];
                $param['mn1'] = @$exp_mnum[0];
                $param['mn2'] = @$exp_mnum[1];
                $param['mn3'] = @$exp_mnum[2];
                $param['mn4'] = @$exp_mnum[3];
                $param['mn5'] = @$exp_mnum[4];
                $param['mn6'] = @$exp_mnum[5];
                $param['operator'] = $operator;
                $param['game'] = $xg_info['id'];
                send_subscribers($param);
                
            }
        }
    }

    private function samAlgo($operator, $game, $wn1, $wn2, $wn3, $wn4, $wn5){
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
}
