<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GreenLottoFetchID;
use App\Models\Operator;
use Illuminate\Support\Facades\DB;

class WescoResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:wesco-result';

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

        $xtime = strtotime($time);

        $drawdate = date('Ymd', time());

        //echo $time;

        if ($xtime > strtotime("9:00:00") && $xtime < strtotime("9:30:00")) {
            //echo '0';
            $drawtime = "08:30:00";
            $timestamp = date('Ymd') . '83000';
        } elseif ($xtime > strtotime("10:30:00") && $xtime < strtotime("11:00:00")) {
            //echo '1';
            $drawtime = "10:00:00";
            $timestamp = date('Ymd') . '100000';
        } elseif ($xtime > strtotime("12:30:00") && $xtime < strtotime("13:00:00")) {
            //echo '2';
            $drawtime = "12:00:00";
            $timestamp = date('Ymd') . '120000';
        } elseif ($xtime > strtotime("14:30:00") && $xtime < strtotime("15:00:00")) {
            //echo '3';
            $drawtime = "14:00:00";
            $timestamp = date('Ymd') . '140000';
        } elseif ($xtime > strtotime("16:30:00") && $xtime < strtotime("17:00:00")) {
            //echo '3';
            $drawtime = "16:00:00";
            $timestamp = date('Ymd') . '160000';
        } elseif ($xtime > strtotime("20:30:00") && $xtime < strtotime("21:00:00")) {
            //echo '1';
            $drawtime = "20:00:00";
            $timestamp = date('Ymd') . '200000';
        } elseif ($xtime > strtotime("20:45:00") && $xtime < strtotime("21:15:00")) {
            //echo '4';
            $drawtime = "20:15:00";
            $timestamp = date('Ymd') . '201500';
        } elseif ($xtime > strtotime("23:15:00") && $xtime < strtotime("23:45:00")) {
            //echo '5';
            $drawtime = "22:45:00";
            $timestamp = date('Ymd') . '224500';
        }

        $xtimestamp = date('Ymd');

        //echo $timestamp.' | '.$drawtime;

        if ($timestamp != '' && $drawtime != '') {
            //check if fetch_id exist
            $query = WescoFetchID::where('fetch_id', "$timestamp")->first();
            if ($query->num_rows() < 1) {
                $xtoken = $this->wesco->auth_token();
                $token = $xtoken['token'];
                $data = array(
                    "ApiKey" => 'MylottoHub',
                    "Mode" => 2,
                    "SequenceId" => "seq-123",
                    "DrawDate" => "$drawdate",
                    "DrawTime" => "$drawtime",
                    "Parms" => "get result",
                    "Timestamp" => "$xtimestamp",
                    "apiUserid" => "MylottoHub",
                    "apiPassword" => "MylottoHub"
                );

                $data_string = json_encode($data);
                $ch = curl_init("http://115.110.148.84:8000/getresultnos/");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json', "Authorization: Bearer $token"
                    )
                );

                $result = curl_exec($ch);
                $result = json_decode($result, true);
                /*print_r($result);
                var_dump($result);
                $call = print_r($data, true);
                echo $call;
                echo $data_string;
                $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
echo $status_code;
                echo $drawdate.' | '.$xtimestamp.' | '.$drawtime;
                echo curl_error($ch);
                exit;*/


                //echo $result['AckResponse']."<br />".$result['AckCode']."<br />".$result['WebResultArray'][0]['GameName']."<br />".$result['WebResultArray'][0]['Result']."<br />".$result['WebResultArray'][0]['MResult'];
                if ($result['status'] == 0) {
                    foreach ($result['webresult_array'] as $item) {
                        $operator = 28;
                        $gl_name = strtoupper(trim($item['game_name']));
                        //get game info
                        $game_info = Operator::where('id', $operator)
                                               ->where('del', '<>', 'Y')
                                               ->where('name',$gl_name )
                                               ->get();

                        if ($game_info->num_rows() > 0) {
                            $xg_info = $game_info;

                            $param['game'] = $xg_info['id'];
                            $param['operator'] = $operator;
                            $param['type'] = 'wesco';
                            $param['date'] = $date("Y-m-d H:i:s", time());
                            $param['wnum'] = $item['result'];
                            $param['mnum'] = $item['mresult'];
                            $rid = $this->operator_model->insert_result($param);
                            //echo $rid;
                            //update user prediction
                            $param['rid'] = $rid;
                            $this->welcome_model->update_prediction($param);
                            $this->forecast_model->post_update($param);
                            $param = array();

                            //ussd action
                            $exp_wnum = explode(',', $item['result']);
                            $exp_mnum = explode(',', $item['mresult']);

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
                            $this->ussd_action->send_subscribers($param);
                            $param = array();
                        }
                    }

                    //insert wesco_fetch_id
                    if (!isset($rid)) {
                        $rid = '1';
                    }
                     WescoFetchID::create([
                        'fetch_id' =>$timestamp,
                        'result' => $rid
                    ]);
                }
            }
        }
    }
}
