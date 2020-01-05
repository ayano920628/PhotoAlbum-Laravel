<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Familyactivation;

class FamilyactivationCreated extends Mailable
{
  use Queueable, SerializesModels;
  protected $activation;
  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(Familyactivation $activation)
  {
    $this->activation = $activation;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $frontendURL = "http://localhost:3000";
    return $this->subject('アカウント有効化メール')
    ->markdown('emails.familyactivations.created')
    ->with([
      'link' => $frontendURL."/familyverify/?code={$this->activation->code}"
    ]);
  }
}
