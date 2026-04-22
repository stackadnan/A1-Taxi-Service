<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DriverResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(protected string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('driver.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ]);

        return (new MailMessage)
            ->subject('Reset Your Driver Password')
            ->greeting('Hello ' . ($notifiable->name ?? 'Driver'))
            ->line('We received a request to reset your driver portal password.')
            ->line('This reset link will expire in 30 minutes.')
            ->action('Reset Password', $resetUrl)
            ->line('If you did not request this, you can safely ignore this email.');
    }
}