<?php

namespace App\Observers;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Notice;
use App\Notifications\NewNotice;
use App\UniversalSearch;
use App\User;
use Illuminate\Support\Facades\Notification;

class NoticeObserver
{
    /**
     * Handle the notice "saving" event.
     *
     * @param  \App\Notice  $notice
     * @return void
     */
    public function saving(Notice $notice)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $notice->company_id = company()->id;
        }
    }

    public function created(Notice $notice){
        if (!isRunningInConsoleOrSeeding()){
            $this->sendNotification($notice);
        }
        $log = new AdminBaseController();
        $log->logSearchEntry($notice->id, 'Notice: ' . $notice->heading, 'admin.notices.edit', 'notice');
    }

    public function updated(Notice $notice) {
        if (!isRunningInConsoleOrSeeding()){
            $this->sendNotification($notice);
        }
    }

    public function sendNotification($notice){
        if ($notice->to == 'employee') {
            if (request()->team_id != '') {
                $users = User::join('employee_details', 'employee_details.user_id', 'users.id')
                    ->where('employee_details.department_id', request()->team_id)->get();
            } else {
                $users = User::allEmployees();
            }

            Notification::send($users, new NewNotice($notice));
        }
        if ($notice->to == 'client') {
            $users = User::join('client_details', 'client_details.user_id', '=', 'users.id')
                ->select('users.id', 'client_details.name', 'client_details.email', 'client_details.created_at')
                ->get();
            Notification::send($users, new NewNotice($notice));
        }
    }

    public function deleting(Notice $notice){
        $universalSearches = UniversalSearch::where('searchable_id', $notice->id)->where('module_type', 'notice')->get();
        if ($universalSearches){
            foreach ($universalSearches as $universalSearch){
                UniversalSearch::destroy($universalSearch->id);
            }
        }
    }
}
