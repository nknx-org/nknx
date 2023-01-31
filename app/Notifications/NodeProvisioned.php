<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NodeProvisioned extends Notification
{
    use Queueable, SerializesModels;
    private $ip;
    private $password;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ip, $password)
    {
        $this->ip = $ip;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Hi '. $notifiable->name . ' 🔔')
            ->subject('FastDeploy node provisioned!')
            ->line('Good News: Your FastDeploy node has finished installing and will be added to your NKNx account in some minutes. We also created a special user you can access using the following credentials:')
            ->line('')
            ->line('IP Address: ' . $this->ip)
            ->line('Username: nknx')
            ->line('Password: ' . $this->password)
            ->action('View my nodes', url('/nodes'))
            ->line('If there are any further questions don\'t hesitate joining our discord server.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }
}
