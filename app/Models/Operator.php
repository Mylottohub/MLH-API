<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'del',
        'play',
        'plink',
        'feature',
        'operator_link',
       'play_order',
      'play_type',
             
    ];

}
