<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetLottoModel extends Model
{
    use HasFactory;

    protected $connection = 'results';

    protected $fillable = [
        'num',
        'date',
        'username',
        'user', 
        'amount', 
        'stake', 
        'line', 
        'mgametype',
        'status',
        'drawAlias', 
        'drawDate', 
        'drawId' , 
        'drawNumber', 
        'drawStatusDesc',
        'drawStatusId',
        'transaction_id', 
        'totalAmount' ,
        'wagerID', 
        'wagerType',
        'amount_won',
        'user_type', 
        'customer_tell', 
        'commission' 
             
    ];
}
