<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserMail extends Mailable
{
  use Queueable, SerializesModels;

  public $user;

  /**
   * Create a new message instance.
   */
  public function __construct($user)
  {
    $this->user = $user;
  }

  public function build()
  {
    return $this->subject('Welkom bij Lucide Inkt')
      ->view('emails.new-user', ['user' => $this->user]);
  }

}
