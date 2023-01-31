<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class IdGenerationStarted extends Notification
{
    use Queueable, SerializesModels;
    private $price;
    private $gen_id_jobs;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($price, $gen_id_jobs)
    {
        $this->price = $price;
        $this->gen_id_jobs = $gen_id_jobs;
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

        $list = 'The needed node ID generation fees have been sent to the following addresses:<br><ul>';
        foreach($this->gen_id_jobs as $gen_id_job){
            $list .= '<li>'.$gen_id_job->address.'</li>';
        }
        $list .= '</ul>';

        $pluralisation = 'node';
        if (count($this->gen_id_jobs)>1){
            $pluralisation = 'nodes';
        }

        return (new MailMessage)
            ->greeting('Hi '. $notifiable->name . ' 🚀')
            ->subject('Your '. $pluralisation .' will be ready soon')
            ->line('Thank you for using the NKNx swap service. The system has successfully processed your payment of ' . $this->price . ' and will instantly start sending the NKN Mainnet tokens out.')
            ->line('You will get informed through our internal notification system when your node starts mining.')
            ->action('View my nodes', url('/nodes'))
            ->line(new HtmlString($list));
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
