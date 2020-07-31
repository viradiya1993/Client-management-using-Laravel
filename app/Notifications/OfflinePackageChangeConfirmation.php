<?php

namespace App\Notifications;

use App\Issue;
use App\OfflinePlanChange;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OfflinePackageChangeConfirmation extends Notification implements ShouldQueue
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $planChange;
    public function __construct(OfflinePlanChange $planChange)
    {
        $this->planChange = $planChange;
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
                    ->subject('Package change request verified.')
                    ->greeting(__('email.hello').'!')
                    ->line('Your request for plan change has been successfully verified.')
                    ->line('Now your package is '.$this->planChange->package->name. ' ('.$this->planChange->package_type.')')
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->planChange->toArray();
    }
}
