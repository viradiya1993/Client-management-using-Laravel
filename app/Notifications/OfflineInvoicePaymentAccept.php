<?php

namespace App\Notifications;

use App\InvoiceSetting;
use App\Issue;
use App\OfflineInvoicePayment;
use App\OfflinePlanChange;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OfflineInvoicePaymentAccept extends Notification implements ShouldQueue
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $payment;
    private $invoiceSetting;
    public function __construct(OfflineInvoicePayment $paymentRequest)
    {
        $this->payment = $paymentRequest;
        $this->invoiceSetting = InvoiceSetting::first();
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
                    ->subject('Ofline payment verified.')
                    ->greeting(__('email.hello').'!')
                    ->line('Your request for '.$this->invoiceSetting->invoice_prefix . ' #' . $this->payment->invoice->id. ' is rejected due to wrong details.')
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
        return $this->payment->toArray();
    }
}
