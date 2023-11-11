<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class FundAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**

     * Get the message envelope.

     */

     public function envelope(): Envelope

     {
 
         return new Envelope(
              subject: 'My Lotto Hub: Wallet Fund',
          );
 
     }

     /**

     * Get the message content definition.

     */

    public function content(): Content

    {

        return new Content(
            view: 'emails.alertFundMail',
        );

    }

  

    /**

     * Get the attachments for the message.

     *

     * @return array

     */

    public function attachments(): array

    {

        return [];

    }
}
