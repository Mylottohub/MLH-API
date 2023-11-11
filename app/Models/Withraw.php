<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withraw extends Model
{
    use HasFactory;

    protected $connection = 'transaction';

    protected $fillable = [
        'user',
        'amount',
        'date',
        'bank',
        'bname',
        'username',
        'ano',
        'aname',
        'status'
       
    ];
}
