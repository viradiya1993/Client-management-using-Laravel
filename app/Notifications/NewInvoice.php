<?php

namespace App\Notifications;

use App\EmailNotificationSetting;
use App\Http\Controllers\Admin\ManageAllInvoicesController;
use App\Invoice;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use App\User;

class NewInvoice extends Notification implements ShouldQueue
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $invoice;
    private $emailSetting;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->emailSetting = EmailNotificationSetting::where('setting_name', 'Invoice Create/Update Notification')->first();
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
        $via = ['database'];

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
        $url = route('front.invoice', md5($this->invoice->id));

        if (($this->invoice->project && !is_null($this->invoice->project->client)) || !is_null($this->invoice->client_id)) {
            // For Sending pdf to email
            $invoiceController = new ManageAllInvoicesController();
            $pdfOption = $invoiceController->domPdfObjectForDownload($this->invoice->id);
            $pdf = $pdfOption['pdf'];
            $filename = $pdfOption['fileName'];

            return (new MailMessage)
                ->subject('New Invoice Generated!')
                ->greeting('Hello ' . ucwords($notifiable->name) . '!')
                ->line('A new invoice for your project has been created as attached. Please click on the link to view the invoice.')
                ->action('View Invoice', $url)
                ->attachData($pdf->output(), $filename . '.pdf');
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
        if(!is_null($this->invoice->project_id)){
            return [
                'project_name' => $this->invoice->project->project_name,
            ];
        }
        else{
            return [
                'invoice_number' => $this->invoice->invoice_number,
            ];
        }
        return $this->invoice->toArray();
    }
}
