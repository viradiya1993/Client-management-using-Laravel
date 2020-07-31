<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AllTimeLogsDataTable;
use App\Helper\Reply;
use App\LogTimeFor;
use App\Project;
use App\ProjectMember;
use App\ProjectTimeLog;
use App\Task;
use App\TaskUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ManageAllTimeLogController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Time Logs';
        $this->pageIcon = 'icon-clock';
        $this->middleware(function ($request, $next) {
            if (!in_array('timelogs', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(AllTimeLogsDataTable $dataTable)
    {
        $this->employees = User::allEmployees();
        $this->projects = Project::all();
        $this->timeLogProjects = $this->projects;
        $this->tasks = Task::all();
        $this->timeLogTasks = $this->tasks;

        $this->logTimeFor = LogTimeFor::first();

        $this->activeTimers = ProjectTimeLog::with('user')
            ->whereNull('end_time')
            ->join('users', 'users.id', '=', 'project_time_logs.user_id');

        if ($this->logTimeFor != null && $this->logTimeFor->log_time_for == 'task') {
            $this->activeTimers = $this->activeTimers->join('tasks', 'tasks.id', '=', 'project_time_logs.task_id');
            $projectName = 'tasks.heading as project_name';
        } else {
            $this->activeTimers = $this->activeTimers->join('projects', 'projects.id', '=', 'project_time_logs.project_id');
            $projectName = 'projects.project_name';
        }

        $this->activeTimers = $this->activeTimers
            ->select('project_time_logs.*', $projectName, 'users.name')
            ->get();

        $this->startDate = Carbon::today()->subDays(15)->format($this->global->date_format);
        $this->endDate = Carbon::today()->addDays(15)->format($this->global->date_format);

        return $dataTable->render('admin.time-logs.index', $this->data);
        // return view('admin.time-logs.index', $this->data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showActiveTimer()
    {
        $this->logTimeFor = LogTimeFor::first();
        $this->activeTimers = ProjectTimeLog::with('user')
            ->whereNull('end_time')
            ->join('users', 'users.id', '=', 'project_time_logs.user_id');

        if ($this->logTimeFor != null && $this->logTimeFor->log_time_for == 'task') {
            $this->activeTimers = $this->activeTimers->join('tasks', 'tasks.id', '=', 'project_time_logs.task_id');
            $projectName = 'tasks.heading as project_name';
        } else {
            $this->activeTimers = $this->activeTimers->join('projects', 'projects.id', '=', 'project_time_logs.project_id');
            $projectName = 'projects.project_name';
        }

        $this->activeTimers = $this->activeTimers
            ->select('project_time_logs.*', $projectName, 'users.name')
            ->get();

        return view('admin.time-logs.show-active-timer', $this->data);
    }

    public function destroy($id)
    {
        ProjectTimeLog::destroy($id);
        return Reply::success(__('messages.timeLogDeleted'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function stopTimer(Request $request)
    {
        $timeId = $request->timeId;
        $timeLog = ProjectTimeLog::findOrFail($timeId);
        $timeLog->end_time = Carbon::now();
        $timeLog->edited_by_user = $this->user->id;
        $timeLog->save();

        $timeLog->total_hours = ($timeLog->end_time->diff($timeLog->start_time)->format('%d') * 24) + ($timeLog->end_time->diff($timeLog->start_time)->format('%H'));

        if ($timeLog->total_hours == 0) {
            $timeLog->total_hours = round(($timeLog->end_time->diff($timeLog->start_time)->format('%i') / 60), 2);
        }
        $timeLog->total_minutes = ($timeLog->total_hours * 60) + ($timeLog->end_time->diff($timeLog->start_time)->format('%i'));

        $timeLog->save();

        $this->activeTimers = ProjectTimeLog::whereNull('end_time')
            ->get();
        $view = view('admin.projects.time-logs.active-timers', $this->data)->render();
        return Reply::successWithData(__('messages.timerStoppedSuccessfully'), ['html' => $view, 'activeTimers' => count($this->activeTimers)]);
    }
    /**
     * @param $projectId
     * @return mixed
     * @throws \Throwable
     */
    public function membersList($projectId)
    {

        $this->members = ProjectMember::byProject($projectId);

        $list = view('admin.tasks.members-list', $this->data)->render();
        return Reply::dataOnly(['html' => $list]);
    }

    public function taskMembersList($taskId)
    {

        $this->members = TaskUser::where('task_id', $taskId)->get();

        $list = view('admin.tasks.members-list', $this->data)->render();
        return Reply::dataOnly(['html' => $list]);
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $id
     */
    public function export($startDate, $endDate, $id, $employee = null)
    {

        $projectName = 'projects.project_name'; // Set default name for select in mysql
        $timeLogs = ProjectTimeLog::join('users', 'users.id', '=', 'project_time_logs.user_id');

        $this->logTimeFor = LogTimeFor::first();

        // Check for apply join Task Or Project
        if ($this->logTimeFor != null && $this->logTimeFor->log_time_for == 'task') {
            $timeLogs = $timeLogs->join('tasks', 'tasks.id', '=', 'project_time_logs.task_id');
            $projectName = 'tasks.heading as project_name';
        } else {
            $timeLogs = $timeLogs->join('projects', 'projects.id', '=', 'project_time_logs.project_id');
        }

        // Fields selecting  For excel
        $timeLogs = $timeLogs->select('project_time_logs.id', 'users.name', $projectName, 'project_time_logs.start_time', 'project_time_logs.end_time', 'project_time_logs.memo', 'project_time_logs.total_minutes');

        // Condition according start_date
        if (!is_null($startDate)) {
            $timeLogs->where(DB::raw('DATE(project_time_logs.`start_time`)'), '>=', "$startDate");
        }

        // Condition according start_date
        if (!is_null($endDate)) {
            $timeLogs->where(DB::raw('DATE(project_time_logs.`end_time`)'), '<=', "$endDate");
        }

        // Condition according employee
        if (!is_null($employee) && $employee !== 'all') {
            $timeLogs->where('project_time_logs.user_id', $employee);
        }

        // Condition according select id
        if (!is_null($id) && $id !== 'all') {
            if ($this->logTimeFor != null && $this->logTimeFor->log_time_for == 'task') {
                $timeLogs->where('project_time_logs.task_id', '=', $id);
            } else {
                $timeLogs->where('project_time_logs.project_id', '=', $id);
            }
        }
        $attributes =  ['total_minutes', 'duration', 'timer'];
        $timeLogs = $timeLogs->get()->makeHidden($attributes);

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'User', 'Log For', 'Start Time', 'End Time', 'Memo', 'Total Hours'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($timeLogs as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('timelog', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Time Log');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('time log file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);

                $sheet->row(1, function ($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold'       =>  true
                    ));
                });
            });
        })->download('xlsx');
    }
}
