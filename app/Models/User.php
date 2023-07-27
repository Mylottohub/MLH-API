<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
       'site_time',
       "ussd_action",
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
