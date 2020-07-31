<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Task;
use App\TaskboardColumn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class AllTasksDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<a href="' . route('admin.all-tasks.edit', $row->id) . '" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                $recurringTaskCount = Task::where('recurring_task_id', $row->id)->count();
                $recurringTask = $recurringTaskCount > 0 ? 'yes' : 'no';

                $action .= '&nbsp;&nbsp;<a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-task-id="' . $row->id . '" data-recurring="' . $recurringTask . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';

                return $action;
            })
            ->editColumn('due_date', function ($row) {
                if ($row->due_date->isPast()) {
                    return '<span class="text-danger">' . $row->due_date->format($this->global->date_format) . '</span>';
                }
                return '<span class="text-success">' . $row->due_date->format($this->global->date_format) . '</span>';
            })
            ->editColumn('users', function ($row) {
                $members = '';
                foreach ($row->users as $member) {
                    $members.= '<a href="' . route('admin.employees.show', [$member->id]) . '">';
                    $members .= '<img data-toggle="tooltip" data-original-title="' . ucwords($member->name) . '" src="' . $member->image_url . '"
                    alt="user" class="img-circle" width="25" height="25"> ';
                    $members.= '</a>';
                }
                return $members;
            })
            ->editColumn('clientName', function ($row) {
                return ($row->clientName) ? ucwords($row->clientName) : '-';
            })
            ->editColumn('created_by', function ($row) {
                if (!is_null($row->created_by)) {
                    return ($row->created_image) ? '<img src="' . asset_url('avatar/' . $row->created_image) . '"
                                                            alt="user" class="img-circle" width="30" height="30"> ' . ucwords($row->created_by) : '<img src="' . asset('img/default-profile-2.png') . '"
                                                            alt="user" class="img-circle" width="30" height="30"> ' . ucwords($row->created_by);
                }
                return '-';
            })
            ->editColumn('heading', function ($row) {
                $name = '<a href="javascript:;" data-task-id="' . $row->id . '" class="show-task-detail">' . ucfirst($row->heading) . '</a>';

                if ($row->is_private) {
                    $name.= ' <i data-toggle="tooltip" data-original-title="' . __('app.private') . '" class="fa fa-lock" style="color: #ea4c89"></i>';
                }
                return $name;
            })
            ->editColumn('column_name', function ($row) {
                return '<label class="label" style="background-color: ' . $row->label_color . '">' . $row->column_name . '</label>';
            })
            ->editColumn('project_name', function ($row) {
                if (is_null($row->project_id)) {
                    return "";
                }
                return '<a href="' . route('admin.projects.show', $row->project_id) . '">' . ucfirst($row->project_name) . '</a>';
            })
            ->rawColumns(['column_name', 'action', 'project_name', 'clientName', 'due_date', 'users', 'created_by', 'heading'])
            ->removeColumn('project_id')
            ->removeColumn('image')
            ->removeColumn('created_image')
            ->removeColumn('label_color');

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Task $model)
    {
        $request = $this->request();
        $startDate = null;
        $endDate = null;
        
        $projectId =  $request->projectId;
        $hideCompleted = $request->hideCompleted;
        $taskBoardColumn = TaskboardColumn::where('slug', 'completed')->first();

        $model = $model->leftJoin('projects', 'projects.id', '=', 'tasks.project_id')
            ->leftJoin('users as client', 'client.id', '=', 'projects.client_id')
            ->join('taskboard_columns', 'taskboard_columns.id', '=', 'tasks.board_column_id')
            ->join('task_users', 'task_users.task_id', '=', 'tasks.id')
            ->leftJoin('users as creator_user', 'creator_user.id', '=', 'tasks.created_by')
            ->select('tasks.id', 'projects.project_name', 'tasks.heading', 'client.name as clientName', 'creator_user.name as created_by', 'creator_user.image as created_image', 'tasks.due_date', 'taskboard_columns.column_name', 'taskboard_columns.label_color', 'tasks.project_id', 'tasks.is_private')
            ->whereNull('projects.deleted_at')
            ->with('users')
            ->groupBy('tasks.id');
            
        if (($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') && $request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $model->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(tasks.`due_date`)'), [$startDate, $endDate]);
                $q->orWhereBetween(DB::raw('DATE(tasks.`start_date`)'), [$startDate, $endDate]);
            });
        }
        if ($projectId != 0 && $projectId !=  null && $projectId !=  'all') {
            $model->where('tasks.project_id', '=', $projectId);
        }
        if ($request->clientID != '' && $request->clientID !=  null && $request->clientID !=  'all') {
            $model->where('projects.client_id', '=', $request->clientID);
        }
        if ($request->assignedTo != '' && $request->assignedTo !=  null && $request->assignedTo !=  'all') {
            $model->where('task_users.user_id', '=', $request->assignedTo);
        }

        if ($request->assignedBY != '' && $request->assignedBY !=  null && $request->assignedBY !=  'all') {
            $model->where('creator_user.id', '=', $request->assignedBY);
        }
        if ($request->status != '' && $request->status !=  null && $request->status !=  'all') {
            $model->where('tasks.board_column_id', '=', $request->status);
        }
        if ($hideCompleted == '1') {
            $model->where('tasks.board_column_id', $taskBoardColumn->id);
        }
        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('allTasks-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-6'l><'col-md-6'Bf>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>")
            ->orderBy(0)
            ->destroy(true)
            ->responsive(true)
            ->serverSide(true)
            ->stateSave(true)
            ->processing(true)
            ->language(__("app.datatable"))
            ->buttons(
                Button::make(['extend'=> 'export','buttons' => ['excel', 'csv']])
            )
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["allTasks-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false ],
            __('app.task') => ['data' => 'heading', 'name' => 'heading'],
            __('app.project')  => ['data' => 'project_name', 'name' => 'projects.project_name'],
            __('modules.tasks.assignTo') => ['data' => 'users', 'name' => 'member.name'],
            __('app.dueDate') => ['data' => 'due_date', 'name' => 'due_date'],
            __('app.status') => ['data' => 'column_name', 'name' => 'taskboard_columns.column_name'],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(150)
                ->addClass('text-center')
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'All_Task_' . date('YmdHis');
    }

    public function pdf()
    {
        set_time_limit(0);
        if ('snappy' == config('datatables-buttons.pdf_generator', 'snappy')) {
            return $this->snappyPdf();
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('datatables::print', ['data' => $this->getDataForPrint()]);

        return $pdf->download($this->getFilename() . '.pdf');
    }
}
