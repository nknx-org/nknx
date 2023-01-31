<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NodeOffline extends Notification
{
    use Queueable, SerializesModels;
    private $node;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($node)
    {
        $this->node = $node;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if(isset($notifiable->notificationPreferences['node_offline'])){
            return $notifiable->notificationPreferences['node_offline'];
        } else{
            return [];
        }
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
            ->subject('One of your nodes went offline')
            ->line('Your node with the IP ' . $this->node->addr .' has stopped mining and needs attention.')
            ->action('View my nodes', url('/nodes'));
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
            'header' => "Node is offline!",
            'excerpt' => "Your node with the IP " . $this->node->addr . " seems to be offline.",
            'text' => "
                <svg viewBox='0 0 1216 364' xmlns='http://www.w3.org/2000/svg' class='logo'><path d='M133.8 146.9l20.1-20.1c19.2-19.2 50.5-19.2 69.7 0l20.1 20.1c19.2 19.2 19.2 50.5 0 69.7l-20.1 20.1c-19.2 19.2-50.5 19.2-69.7 0l-20.1-20.1c-19.1-19.1-19.1-50.5 0-69.7z' fill='#5CE2B2'></path><path d='M596.2 206.4L515.7 74.9h-46.3v214.2h46.3V157.6l80.5 131.5h46.3V74.9h-46.3v131.5zm419.8 0L935.5 74.9h-46.3v214.2h46.3V157.6l80.5 131.5h46.3V74.9H1016v131.5zM809.9 74.9l-51 83.2h-20.3V74.9h-46.3v214.2h46.3V200h19l60 89.1h50.7L797.2 182l65.6-107.1h-52.9zm405.9 87.5h-39.3l-24.7 44.4-24.6-44.4h-39.3l39.1 63.8-38.5 62.9h37.9l26.8-43.7 24.1 43.7h37.9l-38.5-62.9 39.1-63.8z' fill='#242628'></path><path d='M280.2 37.7C255.1 12.6 222 0 188.9 0c-33.1 0-66.2 12.6-91.3 37.7L.1 135.2v34l78.8-.1 62.4-62.4c13.1-13.1 30.4-19.6 47.6-19.6 17.3 0 34.5 6.5 47.6 19.6l62.4 62.4 78.8.1v-34l-97.5-97.5zm-43.8 219.6c-13.1 13.1-30.4 19.6-47.6 19.6-17.3 0-34.5-6.5-47.6-19.6l-62.4-62.4-78.8-.1v34l97.5 97.5c25.1 25.1 58.2 37.7 91.3 37.7 33.1 0 66.2-12.6 91.3-37.7l97.5-97.5v-34l-78.8.1-62.4 62.4z' fill='#5769DF'></path></svg>
                <p>Hi ". $notifiable->name . " 🔔,</p>
                <p>Your node with the IP <b>" . $this->node->addr ."</b> has stopped mining and needs your attention.</p>
                <p>In general a node going offline can have multiple reasons:</p>
                <ul>
                    <li>The node has a very high response time (>1s) due to high CPU utilisation and therefore our crawler couldn't reach it in time</li>
                    <li>The NKN node software crashed because there is no space left on the server to store more blockchain data</li>
                    <li>There are changes in the network settings of your node (dynamic IP change, temporary outtages, etc.)</li>
                    <li>You turned the node off</li>
                </ul>
                <p>If this node stays offline for a longer time there is probably an issue with the server itself. So you definitely should check it. Also be aware that you will only get one offline notification for this node per day so you won't receive any notification about this node's downtime again today.</p>
                <p>Happy mining,<br>Chris (WizardOfCodez)</p>",
        ];
    }
}
