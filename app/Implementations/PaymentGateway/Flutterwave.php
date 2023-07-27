<?php

namespace App\Implementations\PaymentGateway;

use App\Interfaces\PaymentGateway;
use Illuminate\Http\Request;

class Flutterwave implements PaymentGateway
{
    public function verify(Request $request)
    {
        //TODO add some code here
    }

    public function pay(Request $request)
    {
        return 6;
    }

    public function authorize(Request $request)
    {

    }

}
