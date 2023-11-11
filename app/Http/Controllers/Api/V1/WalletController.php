<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Implementations\PaymentGateway\Monnify;
use App\Implementations\WalletGateway\MonnifyWallet;
use App\Models\Transaction;
use App\Models\User;
use App\Models\MonnifyAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    public function debitWallet($user, $details)
    {
        $ref = $this->unique_code(15);
        if (isset($user->id)) {
            $user_id = $user->id;
        } else {
            $user_id = $details['user_id'];
        }

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
        if (isset($details['product_type'])) {
        
            $user_id = MonnifyAccount::where('customerEmail', $details['customerEmail']->first()->id);
            if ($details['product_type'] != 'OFFLINE_PAYMENT_AGENT') {
         
                $channel = 'Monnify';
            } else {
                $channel = 'Moniepoint';
            }
        }else if (isset($details['meta']['user_id'])) {

          $user_id = $details['meta']['user_id'];
        }else{
            $user_id = User::where('email', $details['email']->first()->id);
        }
      
        $amount = $details['amount'] / 100;
        $wallet = User::where('id', $user_id)->first();
        //   Log::info('user: ' . json_encode($wallet));
        //  Log::info('amount: ' . json_encode( $amount));
        //  Log::info('amount: ' . json_encode( $amount));
        if ($wallet) {
            $wallet->wallet += $amount;
            $wallet->save();
        }
        if (isset($details['product_type'])) {
            
            Transaction::create([
                'user' => $user_id,
                'amount' => $amount,
                'date' => $details['date'] ?? date('YmdHis', time()),
                'type' => 'Wallet',
                'description' => "Wallet Fund $user_id ",
                'channel' => $channel,
                'username' => $wallet->username,
                'ref' => $details['reference'],
                'abalance' => $wallet->wallet,

            ]);
        } else {
            if (isset( $details['channel'])){
                $channel = $details['channel'];
            }
            Transaction::create([
                'user' => $user_id,
                'amount' => $amount,
                'date' => $details['date'],
                'type' => 'Wallet',
                'description' => 'Wallet Fund',
                'channel' => $details['meta']['posting'],
                'username' => $wallet->username,
                'ref' => $details['reference'],
                'abalance' => $wallet->wallet,

            ]);
        }
    }

    public function getWalletBalance($user_id)
    {
        $user = $this->getUser($user_id);

        return response()->json([

            "balance" => $user->wallet,
        ], 200);
    }

    public function getWalletBonus($user_id)
    {
        $user = $this->getUser($user_id);

        return response()->json([

            "balance" => $user->bwallet,
        ], 200);
    }

    private function getUser($id)
    {
        return User::where('id', $id)->first();
    }

}

