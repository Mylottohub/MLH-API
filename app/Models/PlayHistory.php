<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator',
        'gameid',
        'num1',
        'num2',
        'num3',
        'num4',
        'num5',
        'num6',
        'num7',
        'num8',
        'num9',
        'num10',
        'date',
        'username',
        'user',
        'num1result',
        'num2result',
        'num3result',
        'num4result',
        'num5result',
        'num1machine',
        'num2machine',
        'num3machine',
        'num4machine',
        'num5machine',
        'result',
        'winamount',
        'gamename',
        'gamecode',
        'gametype',
        'typecode',
        'pid',
        'bid',
        'amount',
        'stake',
        'line',
        'b2',
        'b3',
        'b4',
        'b5',
        'dailygameid',
        'wintype',
        
        
             
    ];
}
