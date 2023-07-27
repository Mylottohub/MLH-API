<?php

namespace App\Jobs;

use App\Mail\ForgetMail;
use App\Mail\RegisterMail;
use App\Mail\VerifyMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mail;

class sendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $user;
    protected $email_template_id;
    protected $otp;

    public function __construct(User $user, $email_template_id, $otp)
    {
        $this->user = $user;
        $this->email_template_id = $email_template_id;
        $this->otp = $otp ?? null;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

      $mailData = [
            'name' => $this->user->name ?? 'user',
            'otp' => $this->otp,
            'img'=> asset('storage/logo_mylotto.png')
            
        ];
        Log::info('Send Mail Queue:' . " working ");
        try {
            if ($this->email_template_id == "register") {
                return $this->sendSuccessMail($mailData);
            }

            if ($this->email_template_id == "verify") {
                return $this->sendVerifyMail($mailData);
            }

            if ($this->email_template_id == "forgot") {
                return $this->sendForgetMail($mailData);
            }

        } catch (Exception $e) {
           
        }

    }

    private function sendVerifyMail($mailData)
    {

        Mail::to($this->user->email)->send(new VerifyMail($mailData));
    }

    private function sendForgetMail($mailData)
    {
        Mail::to($this->user->email)->send(new ForgetMail($mailData));
    }

    private function sendSuccessMail($mailData)
    {
        Mail::to($this->user->email)->send(new RegisterMail($mailData));
    }

   
}
