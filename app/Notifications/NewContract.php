<?php

namespace App\Notifications;

use App\Contract;
use Illuminate\Bus\Queueable;
use App\Traits\SmtpSettings;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewContract extends Notification implements ShouldQueue
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $contract;
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
        $this->setMailConfigs();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('front.contract.show', md5($this->contract->id));

        return (new MailMessage)
            ->subject('New Contract Created!')
            ->greeting('Hello '.ucwords($notifiable->name).'!')
            ->line('A new contract has been created.')
            ->action('View Contract', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->contract->toArray();
    }
}
