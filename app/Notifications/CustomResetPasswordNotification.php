<?php
namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('Wachtwoord resetten'))
            ->view('emails.password-reset', [
                'token' => $this->token,
                'email' => $notifiable->email,
                'first_name' => $notifiable->first_name,
                'last_name' => $notifiable->last_name, // or $notifiable->name
            ]);
    }
}