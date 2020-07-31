<?php

namespace App\Observers;

use App\Notifications\NewChat;
use App\User;
use App\UserChat;

class NewChatObserver
{
    public function created(UserChat $userChat)
    {
        if (!isRunningInConsoleOrSeeding()) {
            // Notify User
            $notifyUser = User::withoutGlobalScope('active')->findOrFail($userChat->user_id);
            $notifyUser->notify(new NewChat($userChat));
        }
    }
}
