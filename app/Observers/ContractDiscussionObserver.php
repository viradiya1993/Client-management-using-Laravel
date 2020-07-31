<?php

namespace App\Observers;

use App\ContractDiscussion;

class ContractDiscussionObserver
{
    public function saving(ContractDiscussion $discussion)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $discussion->company_id = company()->id;
        }
    }
}
