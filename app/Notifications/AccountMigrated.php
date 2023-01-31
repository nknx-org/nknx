<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountMigrated extends Notification
{
    use Queueable, SerializesModels;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {

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
            ->subject('[IMPORTANT] NKNx Crypto payment live!')
            ->line('If you watched our Twitter in the last week closely you might have recognized that we announced to switch our whole payment system to 100% crypto-only.')
            ->line('Given the fact that you\'re receiving this mail that means that this day has come - from now on you can buy all our services with Bitcoin, Litecoin or Monero - isn\'t that awesome?')
            ->line('This also means that all of our subscription-packages are now non-reoccurring - that means that you can always decide if you would like to join us another month without caring about ending your subscription. Just buy another month whenever you feel confident. And don\'t worry - we will send you a reminder 3 days before your subscription runs out, so you have enough time to extend it :)')
            ->line('If you already are an active subscriber your current subscription has been automatically migrated to the new subscription system and also automatically canceled so your credit card won\'t be charged again. As a little bonus we also gave you 1 free additional week on top of your current subscription. Feel free to check out the new and revamped subscription page for more information.')
            ->action('View Your Subscription', url('/user/billing'))
            ->line('Have any questions? Feel free to contact our support at any given time - we would love to answer your questions as fast as possible!');

    }

}
