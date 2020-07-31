<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AllTasksDataTable;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Tasks\StoreTask;
use App\Notifications\TaskReminder;
use App\Project;
use App\ProjectMember;
use App\Task;
use App\TaskboardColumn;
use App\TaskCategory;
use App\TaskFile;
use App\TaskUser;
use App\Traits\ProjectProgress;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ManageAllTasksController extends AdminBaseController
{
    use ProjectProgress;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.tasks';
        $this->pageIcon = 'ti-layout-list-thumb';
        $this->middleware(function ($request, $next) {
            if (!in_array('tasks', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(AllTasksDataTable $dataTable)
    {
        $this->projects = Project::all();
        $this->clients = User::allClients();
        $this->employees = User::allEmployees();
        $this->taskBoardStatus = TaskboardColumn::all();
        $this->startDate = Carbon::today()->subDays(15)->format($this->global->date_format);
        $this->endDate = Carbon::today()->addDays(15)->format($this->global->date_format);

        // return view('admin.tasks.index', $this->data);
        return $dataTable->render('admin.tasks.index', $this->data);
    }

    public function edit($id)
    {
        $this->task = Task::findOrFail($id);
        $this->projects = Project::all();
        $this->employees = User::allEmployees();
        $this->categories = TaskCategory::all();
        $this->taskBoardColumns = TaskboardColumn::all();
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)
                ->where('id', '!=', $id);

            if ($this->task->project_id != '') {
                $this->allTasks = $this->allTasks->where('project_id', $this->task->project_id);
            }

            $this->allTasks = $this->allTasks->get();
        } else {
            $this->allTasks = [];
        }

        return view('admin.tasks.edit', $this->data);
    }

    public function update(StoreTask $request, $id)
    {
        $task = Task::findOrFail($id);
        $oldStatus = TaskboardColumn::findOrFail($task->board_column_id);

        $task->heading = $request->heading;
        if ($request->description != '') {
            $task->description = $request->description;
        }
        $task->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        $task->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $task->task_category_id = $request->category_id;
        $task->priority = $request->priority;
        $task->board_column_id = $request->status;
        $task->dependent_task_id = $request->has('dependent') && $request->dependent == 'yes' && $request->has('dependent_task_id') && $request->dependent_task_id != '' ? $request->dependent_task_id : null;
        $task->is_private = $request->has('is_private') && $request->is_private == 'true' ? 1 : 0;

        $taskBoardColumn = TaskboardColumn::findOrFail($request->status);

        if ($taskBoardColumn->slug == 'completed') {
            $task->completed_on = Carbon::now()->format('Y-m-d H:i:s');
        } else {
            $task->completed_on = null;
        }

        if ($request->project_id != "all") {
            $task->project_id = $request->project_id;
        } else {
            $task->project_id = null;
        }
        $task->save();

        // Sync task users
        $task->users()->sync($request->user_id);

        $this->calculateProjectProgress($request->project_id);

        return Reply::dataOnly(['taskID' => $task->id]);
        //        return Reply::redirect(route('admin.all-tasks.index'), __('messages.taskUpdatedSuccessfully'));
    }

    public function destroy(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // If it is recurring and allowed by user to delete all its recurring tasks
        if ($request->has('recurring') && $request->recurring == 'yes') {
            Task::where('recurring_task_id', $id)->delete();
        }

        $taskFiles = TaskFile::where('task_id', $id)->get();

        foreach ($taskFiles as $file) {
            Files::deleteFile($file->hashname, 'task-files/' . $file->task_id);
            $file->delete();
        }

        Task::destroy($id);
        //calculate project progress if enabled
        $this->calculateProjectProgress($task->project_id);

        return Reply::success(__('messages.taskDeletedSuccessfully'));
    }

    public function create()
    {
        $this->projects = Project::all();
        $this->employees = User::allEmployees();
        $this->categories = TaskCategory::all();
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)->get();
        } else {
            $this->allTasks = [];
        }
        return view('admin.tasks.create', $this->data);
    }

    public function membersList($projectId)
    {
        $this->members = ProjectMember::byProject($projectId);
        $list = view('admin.tasks.members-list', $this->data)->render();
        return Reply::dataOnly(['html' => $list]);
    }

    public function dependentTaskLists($projectId, $taskId = null)
    {
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)
                ->where('project_id', $projectId);

            if ($taskId != null) {
                $this->allTasks = $this->allTasks->where('id', '!=', $taskId);
            }

            $this->allTasks = $this->allTasks->get();
        } else {
            $this->allTasks = [];
        }

        $list = view('admin.tasks.dependent-task-list', $this->data)->render();
        return Reply::dataOnly(['html' => $list]);
    }

    public function store(StoreTask $request)
    {
        $ganttTaskArray = [];
        $gantTaskLinkArray = [];
        $taskBoardColumn = TaskboardColumn::where('slug', 'incomplete')->first();
        $task = new Task();
        $task->heading = $request->heading;
        if ($request->description != '') {
            $task->description = $request->description;
        }
        $task->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        $task->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $task->project_id = $request->project_id;
        $task->task_category_id = $request->category_id;
        $task->priority = $request->priority;
        $task->board_column_id = $taskBoardColumn->id;
        $task->created_by = $this->user->id;
        $task->dependent_task_id = $request->has('dependent') && $request->dependent == 'yes' && $request->has('dependent_task_id') && $request->dependent_task_id != '' ? $request->dependent_task_id : null;
        $task->is_private = $request->has('is_private') && $request->is_private == 'true' ? 1 : 0;

        if ($request->board_column_id) {
            $task->board_column_id = $request->board_column_id;
        }

        if ($taskBoardColumn->slug == 'completed') {
            $task->completed_on = Carbon::now()->format('Y-m-d H:i:s');
        } else {
            $task->completed_on = null;
        }

        $task->save();

        // Sync task users
        $task->users()->sync($request->user_id);

        // For gantt chart
        if ($request->page_name && $request->page_name == 'ganttChart') {
            $parentGanttId = $request->parent_gantt_id;

            $taskDuration = $task->due_date->diffInDays($task->start_date);
            $taskDuration = $taskDuration + 1;

            $ganttTaskArray[] = [
                'id' => $task->id,
                'text' => $task->heading,
                'start_date' => $task->start_date->format('Y-m-d'),
                'duration' => $taskDuration,
                'parent' => $parentGanttId,
                'taskid' => $task->id
            ];

            $gantTaskLinkArray[] = [
                'id' => 'link_' . $task->id,
                'source' => $parentGanttId,
                'target' => $task->id,
                'type' => 1
            ];
        }

        // Add repeated task
        if ($request->has('repeat') && $request->repeat == 'yes') {
            $repeatCount = $request->repeat_count;
            $repeatType = $request->repeat_type;
            $repeatCycles = $request->repeat_cycles;
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');;
            $dueDate = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');;


            for ($i = 1; $i < $repeatCycles; $i++) {
                $repeatStartDate = Carbon::createFromFormat('Y-m-d', $startDate);
                $repeatDueDate = Carbon::createFromFormat('Y-m-d', $dueDate);

                if ($repeatType == 'day') {
                    $repeatStartDate = $repeatStartDate->addDays($repeatCount);
                    $repeatDueDate = $repeatDueDate->addDays($repeatCount);
                } else if ($repeatType == 'week') {
                    $repeatStartDate = $repeatStartDate->addWeeks($repeatCount);
                    $repeatDueDate = $repeatDueDate->addWeeks($repeatCount);
                } else if ($repeatType == 'month') {
                    $repeatStartDate = $repeatStartDate->addMonths($repeatCount);
                    $repeatDueDate = $repeatDueDate->addMonths($repeatCount);
                } else if ($repeatType == 'year') {
                    $repeatStartDate = $repeatStartDate->addYears($repeatCount);
                    $repeatDueDate = $repeatDueDate->addYears($repeatCount);
                }

                $newTask = new Task();
                $newTask->heading = $request->heading;
                if ($request->description != '') {
                    $newTask->description = $request->description;
                }
                $newTask->start_date = $repeatStartDate->format('Y-m-d');
                $newTask->due_date = $repeatDueDate->format('Y-m-d');
                $newTask->project_id = $request->project_id;
                $newTask->task_category_id = $request->category_id;
                $newTask->priority = $request->priority;
                $newTask->board_column_id = $taskBoardColumn->id;
                $newTask->created_by = $this->user->id;
                $newTask->recurring_task_id = $task->id;

                if ($request->board_column_id) {
                    $newTask->board_column_id = $request->board_column_id;
                }

                if ($taskBoardColumn->slug == 'completed') {
                    $newTask->completed_on = Carbon::now()->format('Y-m-d H:i:s');
                } else {
                    $newTask->completed_on = null;
                }

                $newTask->save();

                foreach ($request->user_id as $key => $value) {
                    TaskUser::create(
                        [
                            'user_id' => $value,
                            'task_id' => $newTask->id
                        ]
                    );
                }

                // For gantt chart
                if ($request->page_name && $request->page_name == 'ganttChart') {
                    $parentGanttId = $request->parent_gantt_id;
                    $taskDuration = $newTask->due_date->diffInDays($newTask->start_date);
                    $taskDuration = $taskDuration + 1;

                    $ganttTaskArray[] = [
                        'id' => $newTask->id,
                        'text' => $newTask->heading,
                        'start_date' => $newTask->start_date->format('Y-m-d'),
                        'duration' => $taskDuration,
                        'parent' => $parentGanttId,
                        'users' => [
                            ucwords($newTask->user->name)
                        ],
                        'taskid' => $newTask->id
                    ];

                    $gantTaskLinkArray[] = [
                        'id' => 'link_' . $newTask->id,
                        'source' => $parentGanttId,
                        'target' => $newTask->id,
                        'type' => 1
                    ];
                }

                $startDate = $newTask->start_date->format('Y-m-d');
                $dueDate = $newTask->due_date->format('Y-m-d');
            }
        }

        //calculate project progress if enabled
        $this->calculateProjectProgress($request->project_id);

        if (!is_null($request->project_id)) {
            $this->logProjectActivity($request->project_id, __('messages.newTaskAddedToTheProject'));
        }

        //log search
        $this->logSearchEntry($task->id, 'Task ' . $task->heading, 'admin.all-tasks.edit', 'task');

        if ($request->page_name && $request->page_name == 'ganttChart') {

            return Reply::successWithData(
                'messages.taskCreatedSuccessfully',
                [
                    'tasks' => $ganttTaskArray,
                    'links' => $gantTaskLinkArray
                ]
            );
        }

        if ($request->board_column_id) {
            return Reply::redirect(route('admin.taskboard.index'), __('messages.taskCreatedSuccessfully'));
        }

        return Reply::dataOnly(['taskID' => $task->id]);
        //        return Reply::redirect(route('admin.all-tasks.index'), __('messages.taskCreatedSuccessfully'));
    }

    public function ajaxCreate($columnId)
    {
        $this->projects = Project::all();
        $this->columnId = $columnId;
        $this->employees = User::allEmployees();
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)->get();
        } else {
            $this->allTasks = [];
        }
        return view('admin.tasks.ajax_create', $this->data);
    }

    public function remindForTask($taskID)
    {
        $task = Task::with('user')->findOrFail($taskID);

        // Send  reminder notification to user
        $notifyUser = $task->user;
        $notifyUser->notify(new TaskReminder($task));

        return Reply::success('messages.reminderMailSuccess');
    }

    public function show($id)
    {
        $this->task = Task::with('board_column')->findOrFail($id);
        $view = view('admin.tasks.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function showFiles($id)
    {
        $this->taskFiles = TaskFile::where('task_id', $id)->get();
        return view('admin.tasks.ajax-file-list', $this->data);
    }

    public function history($id)
    {
        $this->task = Task::with('board_column', 'history', 'history.board_column')->findOrFail($id);
        $view = view('admin.tasks.history', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }
}
