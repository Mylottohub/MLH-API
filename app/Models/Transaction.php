<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $connection = 'transaction';

    protected $fillable = [
        'user',
        'amount',
        'date',
        'type',
        'description',
        'username',
        'channel',
        'ref',
        'ref2',
        'abalance',
        'gameIdNumber',
        'gamePlayId',
        'user_type',
        'customer_tell',
        'commission'
       
    ];

}
