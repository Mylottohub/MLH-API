<?php

namespace App\Implementations\Operators;

use App\Interfaces\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use \Carbon\Carbon;

class BabaIjebu implements PaymentGateway
{
    public function getToken(Request $request){

    }

    public function getActiveGames(Request $request){
        
    }

    public function playGame(Request $request){
        
    }

    public function getResult(Request $request){
        
    }

    public function getPrizeList(Request $request){
        
    }

}