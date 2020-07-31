<?php

namespace App\Observers;

use App\Events\TaskEvent;
use App\Events\TaskUpdated as EventsTaskUpdated;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Notifications\TaskCompleted;
use App\Notifications\TaskUpdated;
use App\Notifications\TaskUpdatedClient;
use App\Task;
use App\TaskboardColumn;
use App\Traits\ProjectProgress;
use App\UniversalSearch;
use App\User;
use Illuminate\Support\Facades\Notification;

class TaskObserver
{
    use ProjectProgress;

    public function saving(Task $task)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $task->company_id = company()->id;
        }
    }

    public function creating(Task $task)
    {
        if (company()) {
            $taskBoard = TaskboardColumn::orderBy('id', 'asc')->first();
            $task->board_column_id = $taskBoard->id;
        }
    }

    public function updating(Task $task)
    {
        if ($task->isDirty('status')) {
            $status = $task->status;

            if ($status == 'completed') {
                $task->board_column_id = TaskboardColumn::where('priority', 2)->first()->id;
                $task->column_priority = 1;
            } elseif ($status == 'incomplete') {
                $task->board_column_id = TaskboardColumn::where('priority', 1)->first()->id;
                $task->column_priority = 1;
            }
        }

        if ($task->isDirty('board_column_id') && $task->column_priority != 2) {
            $task->status = 'incomplete';
        }
    }

    public function created(Task $task)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (request()->has('project_id') && request()->project_id != "all" && request()->project_id != '') {
                if ($task->project->client_id != null && $task->project->allow_client_notification == 'enable' && $task->project->client->status != 'deactive') {
                    event(new TaskEvent($task, $task->project->client, 'NewClientTask'));
                }
            }
            $log = new AdminBaseController();
            if (\user()) {
                $log->logTaskActivity($task->id, user()->id, "createActivity", $task->board_column_id);
            }

            if ($task->project_id) {
                //calculate project progress if enabled
                $log->logProjectActivity($task->project_id, __('messages.newTaskAddedToTheProject'));
                $this->calculateProjectProgress($task->project_id);
            }

            //log search
            $log->logSearchEntry($task->id, 'Task: ' . $task->heading, 'admin.all-tasks.edit', 'task');

            //Send notification to user
            event(new TaskEvent($task, $task->users, 'NewTask'));
        }
    }

    public function updated(Task $task)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if ($task->isDirty('board_column_id')) {

                $taskBoardColumn = TaskboardColumn::findOrFail($task->board_column_id);

                if ($taskBoardColumn->slug == 'completed') {
                    // send task complete notification
                    event(new TaskEvent($task, $task->users, 'TaskUpdated'));

                    $admins = User::allAdmins($task->user_id);
                    event(new TaskEvent($task, $admins, 'TaskCompleted'));

                    if (request()->project_id && request()->project_id != "all") {
                        if ($task->project->client_id != null && $task->project->allow_client_notification == 'enable') {
                            event(new TaskEvent($task, $task->project->client, 'TaskCompleted'));
                        }
                    }
                }
            }

            if (request('user_id')) {
                //Send notification to user
                event(new TaskEvent($task, $task->users, 'TaskUpdated'));
                if ((request()->project_id != "all") && !is_null($task->project)) {
                    if ($task->project->client_id != null && $task->project->allow_client_notification == 'enable' && $task->project->client->status != 'deactive') {
                        event(new TaskEvent($task, $task->project->client, 'TaskUpdatedClient'));
                    }
                }
            }
        }

        if (!request()->has('draggingTaskId') && !request()->has('draggedTaskId')) {
            event(new EventsTaskUpdated($task));
        }

        if (\user()) {
            $log = new AdminBaseController();
            $log->logTaskActivity($task->id, user()->id, "updateActivity", $task->board_column_id);
        }

        if ($task->project_id) {
            //calculate project progress if enabled
            $this->calculateProjectProgress($task->project_id);
        }
    }

    public function deleting(Task $task)
    {
        $universalSearches = UniversalSearch::where('searchable_id', $task->id)->where('module_type', 'task')->get();
        if ($universalSearches) {
            foreach ($universalSearches as $universalSearch) {
                UniversalSearch::destroy($universalSearch->id);
            }
        }
    }
}
