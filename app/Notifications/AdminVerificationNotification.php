<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Verifikasi Akun Admin Dropship Solution')
            ->line('Akun admin Anda telah diverifikasi.')
            ->line('Anda sekarang dapat login dan mengakses dashboard admin.')
            ->action('Login', url('/login'))
            ->line('Terima kasih telah menggunakan aplikasi kami!');
    }
}