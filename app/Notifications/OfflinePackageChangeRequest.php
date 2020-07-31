<?php

namespace App\Notifications;

use App\Company;
use App\Issue;
use App\OfflinePlanChange;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OfflinePackageChangeRequest extends Notification implements ShouldQueue
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $planChange;
    private $company;
    public function __construct($company, OfflinePlanChange $planChange)
    {
        $this->planChange = $planChange;
        $this->company = $company;
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
        return ['mail', 'database'];
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
            ->subject('Package change request.')
            ->greeting(__('email.hello').' '.ucwords($notifiable->name).'!')
            ->line($this->company->company_name. ' is requested for package change.')
            ->line('Package Name: '.$this->planChange->package->name. ' ('.$this->planChange->package_type.').')
             ->line(__('email.thankyouNote'));
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
        return $this->company->toArray();
    }
}
