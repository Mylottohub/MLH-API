<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface PaymentGateway
{
    public function verify(Request $request);

    public function pay($postDetails);

    public function webhook(Request $request);

    public function authorize(Request $request);

}
