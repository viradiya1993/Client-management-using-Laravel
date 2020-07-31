<?php

namespace App\Notifications;

use App\EmailNotificationSetting;
use App\Payment;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;

class NewPayment extends Notification implements ShouldQueue
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $payment;
    private $emailSetting;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->emailSetting = EmailNotificationSetting::where('setting_name', 'Payment Create/Update Notification')->first();
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
        $via = [];

        if($this->emailSetting->send_email == 'yes'){
            array_push($via, 'mail');
        }

        if($this->emailSetting->send_slack == 'yes'){
            array_push($via, 'slack');
        }

        if($this->emailSetting->send_push == 'yes'){
            array_push($via, OneSignalChannel::class);
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
//        $url = route('client.payments.index');

        if (($this->payment->project_id && $this->payment->project->client_id != null) || ($this->payment->invoice_id && $this->payment->invoice->client_id != null)) {
            $url = route('front.invoice', md5($this->payment->invoice_id));
            return (new MailMessage)
                ->subject('Payment Received!')
                ->greeting('Hello ' . ucwords($notifiable->name) . '!')
                ->line('Thank you. Payment is recorded. Please click on the link below to view/download the paid invoice.')
                ->action('View Invoice', $url);
        }

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->payment->toArray();
    }
}
