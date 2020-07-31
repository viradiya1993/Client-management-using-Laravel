<?php

namespace App\Observers;


use App\Invoice;
use App\Notifications\InvoicePaymentReceived;
use App\Notifications\NewInvoice;
use App\UniversalSearch;
use App\User;
use Illuminate\Support\Facades\Notification;

class InvoiceObserver
{

    public function saving(Invoice $invoice)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $invoice->company_id = company()->id;
        }
    }

    public function saved(Invoice $invoice)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (($invoice->project && $invoice->project->client_id != null) || $invoice->client_id != null) {
                $clientId = ($invoice->project && $invoice->project->client_id != null) ? $invoice->project->client_id : $invoice->client_id;
                // Notify client
                $notifyUser = User::withoutGlobalScopes(['company', 'active'])->findOrFail($clientId);
                $notifyUser->notify(new NewInvoice($invoice));
            }

            if($invoice->isDirty('status'))
            {
                $admins = User::allAdmins();
                Notification::send($admins, new InvoicePaymentReceived($invoice));
            }
        }
    }

    public function deleting(Invoice $invoice){
        $universalSearches = UniversalSearch::where('searchable_id', $invoice->id)->where('module_type', 'invoice')->get();
        if ($universalSearches){
            foreach ($universalSearches as $universalSearch){
                UniversalSearch::destroy($universalSearch->id);
            }
        }
    }

}
