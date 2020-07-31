<?php

namespace App\Observers;

use App\ClientPayment;
use App\Invoice;
use App\Notifications\InvoicePaymentReceived;
use App\User;
use Illuminate\Support\Facades\Notification;

class InvoicePaymentReceivedObserver
{
    public function created(ClientPayment $payment)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $admins = User::allAdmins();
            $invoice = Invoice::findOrFail($payment->invoice_id);

            if($invoice){
                Notification::send($admins, new InvoicePaymentReceived($invoice));
            }
        }
    }
}
