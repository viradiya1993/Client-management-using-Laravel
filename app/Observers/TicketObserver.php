<?php

namespace App\Observers;

use App\Notifications\NewTicket;
use App\Ticket;
use App\UniversalSearch;
use App\User;
use Illuminate\Support\Facades\Notification;

class TicketObserver
{

    public function created(Ticket $ticket)
    {
        if (!isRunningInConsoleOrSeeding()) {
            //send admin notification
            Notification::send(User::allAdmins(), new NewTicket($ticket));
        }
    }

    public function saving(Ticket $ticket)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $ticket->company_id = company()->id;
        }
    }

    public function deleting(Ticket $ticket){
        $universalSearches = UniversalSearch::where('searchable_id', $ticket->id)->where('module_type', 'ticket')->get();
        if ($universalSearches){
            foreach ($universalSearches as $universalSearch){
                UniversalSearch::destroy($universalSearch->id);
            }
        }
    }

}
