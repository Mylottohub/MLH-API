<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PlayHistory;

class WinCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:win-calculation';

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
        $result = PlayHistory::all();
        if ($result != FALSE) {
            foreach ($result as $item) {
                //win array
                $warr[] = $item['num1result'];
                $warr[] = $item['num2result'];
                $warr[] = $item['num3result'];
                $warr[] = $item['num4result'];
                $warr[] = $item['num5result'];
                $bno = 0;
                if (in_array($item['num1'], $warr)) {
                    $bno = $bno + 1;
                }
                if (in_array($item['num2'], $warr)) {
                    $bno = $bno + 1;
                }
                if (in_array($item['num3'], $warr)) {
                    $bno = $bno + 1;
                }
                if (in_array($item['num4'], $warr)) {
                    $bno = $bno + 1;
                }
                if (in_array($item['num5'], $warr)) {
                    $bno = $bno + 1;
                }
                if (in_array($item['num6'], $warr)) {
                    $bno = $bno + 1;
                }
                if (in_array($item['num7'], $warr)) {
                    $bno = $bno + 1;
                }
                if (in_array($item['num8'], $warr)) {
                    $bno = $bno + 1;
                }
                if (in_array($item['num9'], $warr)) {
                    $bno = $bno + 1;
                }
                if (in_array($item['num10'], $warr)) {
                    $bno = $bno + 1;
                }

                if ($bno == 2 && $item['b2'] != 0) {
                    $param['id'] = $item['id'];
                    $param['wintype'] = '2 Balls';
                    $param['winamount'] = $item['b2'];
                    $param['user'] = $item['user'];
                    PlayHistory::updateOrCreate(
                        [ 'user' => $item['user'],
                            'id' => $item['id']
                    ],
                        [
                        'wintype' => $param['wintype'],
                        'winamount' =>  $param['winamount'],
                                               
                       ]);
                } elseif ($bno == 3 && $item['b3'] != 0) {
                    $param['id'] = $item['id'];
                    $param['wintype'] = '3 Balls';
                    $param['winamount'] = $item['b3'];
                    $param['user'] = $item['user'];
                    PlayHistory::updateOrCreate(
                        [ 'user' => $item['user'],
                            'id' => $item['id']
                    ],
                        [
                        'wintype' => $param['wintype'],
                        'winamount' =>  $param['winamount'],
                                               
                       ]);
                } elseif ($bno == 4 && $item['b4'] != 0) {
                    $param['id'] = $item['id'];
                    $param['wintype'] = '4 Balls';
                    $param['winamount'] = $item['b4'];
                    $param['user'] = $item['user'];
                    PlayHistory::updateOrCreate(
                        [ 'user' => $item['user'],
                            'id' => $item['id']
                    ],
                        [
                        'wintype' => $param['wintype'],
                        'winamount' =>  $param['winamount'],
                                               
                       ]);
                } elseif ($bno == 5 && $item['b5'] != 0) {
                    $param['id'] = $item['id'];
                    $param['wintype'] = '5 Balls';
                    $param['winamount'] = $item['b5'];
                    $param['user'] = $item['user'];
                    PlayHistory::updateOrCreate(
                        [ 'user' => $item['user'],
                            'id' => $item['id']
                    ],
                        [
                        'wintype' => $param['wintype'],
                        'winamount' =>  $param['winamount'],
                                               
                       ]);
                } else {
                    $param['id'] = $item['id'];
                    $param['wintype'] = 'None';
                    $param['winamount'] = 0;
                    PlayHistory::updateOrCreate(
                        [ 'user' => $item['user'],
                            'id' => $item['id']
                    ],
                        [
                        'wintype' => $param['wintype'],
                        'winamount' =>  $param['winamount'],
                                               
                       ]);
                }
            }
        }
    }
}
