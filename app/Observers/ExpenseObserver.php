<?php

namespace App\Observers;

use App\Expense;
use App\Notifications\NewExpenseAdmin;
use App\Notifications\NewExpenseMember;
use App\Notifications\NewExpenseStatus;
use App\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;

class ExpenseObserver
{

    public function created(Expense $expense)
    {
        if (!isRunningInConsoleOrSeeding()) {
            // Default status is approved means it is posted by admin
            if ($expense->status == 'approved') {
                $expense->user->notify(new NewExpenseMember($expense));
            }

            // Default status is pending that mean it is posted by member
            if ($expense->status == 'pending') {
                Notification::send(User::allAdmins(), new NewExpenseAdmin($expense));
            }
        }
    }

    public function updated(Expense $expense)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if ($expense->isDirty('status')) {
                $expense->user->notify(new NewExpenseStatus($expense));
            }
        }
    }

    public function saving(Expense $expense)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $expense->company_id = company()->id;
        }
    }

    public function deleting(Expense $expense)
    {
        File::delete('user-uploads/expense-invoice/' . $expense->bill);
    }

    public function updating(Expense $expense)
    {
        $original = $expense->getOriginal();
        if ($expense->isDirty('bill')) {
            File::delete('user-uploads/expense-invoice/' . $original['bill']);
        }
    }
}
