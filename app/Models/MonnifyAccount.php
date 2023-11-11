<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonnifyAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractCode',
        'accountReference',
        'accountName',
        'currencyCode',
        'customerEmail',
        'accountNumber',
        'bankName',
       'bankCode',
      'reservationReference',
      'status',
      'createdOn',
      'user',
      'megzy'
             
    ];

}
