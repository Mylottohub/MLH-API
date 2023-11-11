<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'winning_number',
        'machine_number',
        'game',
        'operator',
       'date',
       'year',
        'month',
        'winning_total',
        'machine_total',
        'winning_num1',
        'winning_num2',
        'winning_num3',
        'winning_num4',
        'winning_num5',
        'winning_num6',
        'machine_num1',
        'machine_num2',
        'machine_num3',
        'machine_num4',
        'machine_num5',
       'machine_num6',
     'machine_num6',
       
             
    ];

}
