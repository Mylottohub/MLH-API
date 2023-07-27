<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Implementations\PaymentGateway\Monnify;
use App\Implementations\WalletGateway\MonnifyWallet;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    public function debitWallet($details)
    {
        $ref = $this->unique_code(15);
        $user_id = $details['user_id'];
        $wallet = Wallet::where('user_id', $user_id)->first();
        $amount = 3;

        if ($wallet->balance < 1 || $wallet->balance < $amount) {
            return false;
        } else {
            
            $wallet->balance -= $amount;
            $wallet->save();
            $transaction = Transaction::create([
                'user_id' => $user_id,
                'merchant_id' => $merchant_id->merchant_id,
                'activity' => "SMS Tranction from $user_id",
                'date' => Carbon::now(),
                'amount' => $amount,
                'ref' => $ref,
                'status' => ($posting = "Success") ? 'Pending' : 'Success',
                'payment_method' => "Wallet Transaction",
            ]);
        }
        return $transaction;
    }

    public function topUpWallet($details)
    {
        $user_id = $details['meta']['user_id'];
        
        $amount = $details['amount']/100;
        $wallet = User::where('id', $user_id)->first();
     //   Log::info('user: ' . json_encode($wallet));
      //  Log::info('amount: ' . json_encode( $amount));
        if ($wallet) {
            $wallet->wallet += $amount;
            $wallet->save();
     //       Log::info('wallet after: ' . json_encode($wallet));
            Transaction::create([
                'user'=> $user_id,
                'amount' => $amount,
                 'date'=> $details['date'],
                 'type' => 'Wallet',
                 'description' => 'Wallet Fund',
                 'channel' => $details['meta']['posting'],
                 'username'=> $wallet->username,
                 'ref' => $details['reference'],
                 'abalance' => $wallet->wallet,
                
            ]);
        }
    }
}