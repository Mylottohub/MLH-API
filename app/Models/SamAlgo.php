<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SamAlgo extends Model
{
    use HasFactory;

    protected $connection = 'results';

    protected $fillable = [
        'operator',
        'date',
        'game',
        'num', 
        'numresult', 
           
    ];

  
}
