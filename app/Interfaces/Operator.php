<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface Operator
{
    public function getToken(Request $request);

    public function getActiveGames(Request $request);

    public function playGame($parameters, Request $request );

    public function getResult(Request $request);

    public function getPrizeList(Request $request);

    public function ticketValidation(Request $request);

}