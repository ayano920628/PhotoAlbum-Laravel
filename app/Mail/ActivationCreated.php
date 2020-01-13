<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Activation;


class ActivationCreated extends Mailable
{
  use Queueable, SerializesModels;

  protected $activation;
  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(Activation $activation) {
    $this->activation = $activation;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $frontendURL = "http://www.photoalbum.com.s3-website-ap-northeast-1.amazonaws.com";
    return $this->from('hello@example.com','PhotoAlbum')
    ->subject('アカウント有効化メール')
    ->markdown('emails.activations.created')
    ->with([
      'link' => $frontendURL."/verify/?code={$this->activation->code}"
    ]);
  }
}
