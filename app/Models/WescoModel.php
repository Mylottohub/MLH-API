<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WescoModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'num',
        'date',
        'username',
        'user', 
        'amount', 
        'stake', 
        'line', 
        'mgametype',
        'drawname', 
        'drawdate', 
        'drawid' , 
        'closetime', 
        'transaction_id', 
        'totalamount' ,
        'TikcetId', 
        'user_type', 
        'customer_tell', 
        'commission' 
             
    ];
}
