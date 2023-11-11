<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WescoFetchID extends Model
{
    use HasFactory;

    protected $table = 'wesco_fetch_id';

    protected $connection = 'results';

    protected $fillable = [
        'fetch_id',
        'result',
    ];
}
