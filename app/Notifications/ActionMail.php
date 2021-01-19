<?php

namespace App\Notifications;

use App\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActionMail extends Notification
{
    use Queueable;

    private $action;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Action $action)
    {
        $this->action = $action;
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
        return (new MailMessage())
            ->subject(config('app.name') . ' | ' . __('Action has triggered'))
            ->line(sprintf(__('Action "%s" triggered for sensor "%s".'), $this->action->name, $this->action->sensor->name))
            ->line(sprintf(__('Sensor limits: %s - %s.'), $this->action->sensor->min_value, $this->action->sensor->max_value))
            ->line(sprintf(__('Received value: %s.'), $this->action->sensor->value));
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
            //
        ];
    }
}
