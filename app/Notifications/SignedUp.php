<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SignedUp extends Notification
{
    use Queueable;
    private $user;

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
        return ['mail','database'];
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
                    ->greeting('Hi '. $notifiable->name . ' 👋')
                    ->subject('Welcome to NKNx')
                    ->line('Thanks for signing up. We’re thrilled to have you on board.')
                    ->action('Sign in to your account', url('/'))
                    ->line('If you did not sign up for this account you can ignore this email and the account will be deleted automatically after 5 days.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'header' => "Welcome to NKNx",
            'excerpt' => "We're glad you're on board!",
            'text' => "
                <svg viewBox='0 0 1216 364' xmlns='http://www.w3.org/2000/svg' class='logo'><path d='M133.8 146.9l20.1-20.1c19.2-19.2 50.5-19.2 69.7 0l20.1 20.1c19.2 19.2 19.2 50.5 0 69.7l-20.1 20.1c-19.2 19.2-50.5 19.2-69.7 0l-20.1-20.1c-19.1-19.1-19.1-50.5 0-69.7z' fill='#5CE2B2'></path><path d='M596.2 206.4L515.7 74.9h-46.3v214.2h46.3V157.6l80.5 131.5h46.3V74.9h-46.3v131.5zm419.8 0L935.5 74.9h-46.3v214.2h46.3V157.6l80.5 131.5h46.3V74.9H1016v131.5zM809.9 74.9l-51 83.2h-20.3V74.9h-46.3v214.2h46.3V200h19l60 89.1h50.7L797.2 182l65.6-107.1h-52.9zm405.9 87.5h-39.3l-24.7 44.4-24.6-44.4h-39.3l39.1 63.8-38.5 62.9h37.9l26.8-43.7 24.1 43.7h37.9l-38.5-62.9 39.1-63.8z' fill='#242628'></path><path d='M280.2 37.7C255.1 12.6 222 0 188.9 0c-33.1 0-66.2 12.6-91.3 37.7L.1 135.2v34l78.8-.1 62.4-62.4c13.1-13.1 30.4-19.6 47.6-19.6 17.3 0 34.5 6.5 47.6 19.6l62.4 62.4 78.8.1v-34l-97.5-97.5zm-43.8 219.6c-13.1 13.1-30.4 19.6-47.6 19.6-17.3 0-34.5-6.5-47.6-19.6l-62.4-62.4-78.8-.1v34l97.5 97.5c25.1 25.1 58.2 37.7 91.3 37.7 33.1 0 66.2-12.6 91.3-37.7l97.5-97.5v-34l-78.8.1-62.4 62.4z' fill='#5769DF'></path></svg>
                <p>Hi ". $notifiable->name . " 👋,</p>
                <p>Welcome to NKNx!</p>
                <p>When you're reading this that means you already discovered our notification-center - good work! To help you get started fast here are some quick information on how to use our service:</p>
                <p>On the <b>left sidebar</b> you can switch between your dashboard, the wallet tracker, the node manager and FastDeploy. You can add your NKN mainnet wallets as well as your existing nodes easily by hitting the corresponding button. If you are running aa node that <b>changes its IP regularly</b> (f.e. on a home network) it is good to know that you are also able to add DNS-names as your node address. Combined with a dynamic DNS service you can also watch these nodes easily.</p>
                <p>If you are struggling with getting mainnet tokens for your node to generate an ID it is worth trying out our <b>Node ID generation service</b>. This service gives you an easy way of giving your node a quick start without the hassle of swapping NKN tokens from exchanges to mainnet.</p>
                <p><b>FastDeploy</b> makes creating nodes easily. You can either generate a custom installation script for every linux-based architecture or directly deploying to your favorite VPS providers. You should also definitely have a look at your <a href=\"/user/profile\">User Account</a>. Here you can add your VPS API keys and also your public SSH keys. Every SSH key will get added to every FastDeploy node you create. This way you can make sure that only selected PCs can access it.</p>
                <p>Finally a personal note from me, the Wiz:</p>
                <p>I worked very hard on creating NKNx. I love what I do and believe in the fact that mining on the NKN network should be as easy as it can be. We hope you are enjoying NKNx - if there is anything I can help with feel free to contact me on the official NKN discord server (link also in the left sidebar).</p>
                <p>Happy mining,<br>Chris (WizardOfCodez)</p>",
            ];
    }
}
