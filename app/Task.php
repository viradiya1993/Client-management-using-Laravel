<?php

namespace App;

use App\Observers\TaskObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Task extends BaseModel
{
    protected $fillable = ['board_column_id'];
    use Notifiable;

    protected static function boot()
    {
        parent::boot();

        static::observe(TaskObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function routeNotificationForMail()
    {
        return $this->user->email;
    }

    protected $dates = ['due_date', 'completed_on', 'start_date'];
    protected $appends = ['due_on', 'create_on'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function board_column()
    {
        return $this->belongsTo(TaskboardColumn::class, 'board_column_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_users');
    }

    public function create_by()
    {
        return $this->belongsTo(User::class, 'created_by')->withoutGlobalScopes(['active']);
    }

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function subtasks()
    {
        return $this->hasMany(SubTask::class, 'task_id');
    }

    public function history()
    {
        return $this->hasMany(TaskHistory::class, 'task_id')->orderBy('id', 'desc');
    }

    public function completedSubtasks()
    {
        return $this->hasMany(SubTask::class, 'task_id')->where('sub_tasks.status', 'complete');
    }

    public function incompleteSubtasks()
    {
        return $this->hasMany(SubTask::class, 'task_id')->where('sub_tasks.status', 'incomplete');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id')->orderBy('id', 'desc');
    }

    public function files()
    {
        return $this->hasMany(TaskFile::class, 'task_id');
    }


    /**
     * @return string
     */
    public function getDueOnAttribute()
    {
        if (!is_null($this->due_date)) {
            return $this->due_date->format('d M, y');
        }
        return "";
    }
    public function getCreateOnAttribute()
    {
        if (!is_null($this->start_date)) {
            return $this->start_date->format('d M, y');
        }
        return "";
    }

    /**
     * @param $projectId
     * @param null $userID
     */
    public static function projectOpenTasks($projectId, $userID = null)
    {
        $taskBoardColumn = TaskboardColumn::where('slug', '<>','completed')->first();
        $projectTask = Task::join('task_users', 'task_users.task_id', '=', 'tasks.id')->where('tasks.board_column_id', $taskBoardColumn->id)->select('tasks.*');

        if ($userID) {
            $projectIssue = $projectTask->where('task_users.user_id', '=', $userID);
        }

        $projectIssue = $projectTask->where('project_id', $projectId)
            ->get();

        return $projectIssue;
    }

    public static function projectCompletedTasks($projectId)
    {
        $taskBoardColumn = TaskboardColumn::where('slug', 'completed')->first();
        return Task::where('tasks.board_column_id', $taskBoardColumn->id)
            ->where('project_id', $projectId)
            ->get();
    }
}
