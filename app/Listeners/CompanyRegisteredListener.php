<?php

namespace App\Listeners;

use App\Events\CompanyRegistered;
use App\Notifications\NewCompanyRegister;
use App\User;
use Illuminate\Support\Facades\Notification;

class CompanyRegisteredListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CompanyRegistered  $event
     * @return void
     */
    public function handle(CompanyRegistered $event)
    {
        $company = $event->company;

        $superAdmin = User::whereNull('company_id')->get();
        Notification::send($superAdmin, new NewCompanyRegister($company));
    }
}
