<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'agency';

    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'dob',
        'gender',
        'tell',
         'status',
        'confirmation_code',
        'confirmed',
        'date',
        'wallet',
        'role',
        'type',
       'wwallet',
      'bwallet',
        'bank',
        'bname',
        'accno',
       'accname',
        'state',
        'pix',
        'lga',
        'country',
        'ref',
        'ccommission',
        'pcommission',
        'auser',
        'is_robot',
        'games',
       'num_pos',
       'num_pos2',
        'num_pos1',
        'num_pos3',
        "ussd_action",
       'site_time',
        'first_name',
        'last_name',
        'address',
        'gl_bwallet',
       'sl_bwallet',
       'gh_bwallet',
        'lm_bwallet',
        'we_bwallet',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}


