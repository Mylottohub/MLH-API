<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LottoManiaModel extends Model
{
    use HasFactory;

    protected $connection = 'results';

    protected $fillable = [
        'num' , 
        'date', 
        'username' ,
         'user' , 
         'amount' , 
         'stake' , 
         'line', 
         'GameType', 
         'GameTypeName' , 
         'GameId', 
         'GameName' , 
         'DrawTime' , 
         'DrawId' , 
         'TranId', 
         'TSN' , 
         'SessionId', 
         'balance' , 
         'SelectionType', 
         'mgametype' , 
         'operator_type' , 
         'status',
         'double_chance' , 
         'user_type' , 
         'customer_tell' , 
         'commission'
             
    ];
}
