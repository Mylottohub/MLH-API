<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPredictions extends Model
{
    use HasFactory;

    protected $connection = 'results';

    protected $fillable = [
        'operator',
        'date',
        'username',
        'user', 
        'game', 
        'num1', 
        'num2', 
        'num3',
        'num4',
        'num5', 
        'num1result', 
        'num2result' , 
        'num3result', 
        'num4result',
        'num5result',
        'num1machine', 
        'num2machine' ,
        'num3machine', 
        'num4machine',
        'num5machine',
        'totalpoint', 
        'result', 
      
             
    ];


}
