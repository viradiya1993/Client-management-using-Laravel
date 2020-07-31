<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Payment;
use App\Project;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class ProjectsDataTable extends BaseDataTable
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
            ->addColumn('action', function ($row) {
                $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn " type="button"><i class="ti-more"></i> </button>
                <ul role="menu" class="dropdown-menu pull-right">
                  <li><a href="' . route('admin.projects.edit', [$row->id]) . '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                  <li><a href="' . route('admin.projects.show', [$row->id]) . '"><i class="fa fa-search" aria-hidden="true"></i> ' . trans('app.view') . ' ' . trans('app.details') . '</a></li>
                  <li><a href="' . route('admin.projects.gantt', [$row->id]) . '"><i class="fa fa-bar-chart" aria-hidden="true"></i> ' . trans('modules.projects.viewGanttChart') . '</a></li>
                  <li><a href="' . route('front.gantt', [md5($row->id)]) . '" target="_blank"><i class="fa fa-line-chart" aria-hidden="true"></i> ' . trans('modules.projects.viewPublicGanttChart') . '</a></li>
                  <li><a href="javascript:;" data-user-id="' . $row->id . '" class="archive"><i class="fa fa-archive" aria-hidden="true"></i> Archive</a></li>
                  <li><a href="javascript:;" data-user-id="' . $row->id . '" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>';

                $action .= '</ul> </div>';

                return $action;
            })
            ->addColumn('members', function ($row) {
                $members = '';

                if (count($row->members) > 0) {
                    foreach ($row->members as $member) {
                        $members .= '<img data-toggle="tooltip" data-original-title="' . ucwords($member->user->name) . '" src="' . $member->user->image_url . '"
                        alt="user" class="img-circle" width="25" height="25"> ';
                    }
                } else {
                    $members .= __('messages.noMemberAddedToProject');
                }
                $members .= '<a class="btn btn-primary btn-circle" style="width: 25px;height: 25px;padding: 3px;" data-toggle="tooltip" data-original-title="' . __('modules.projects.addMemberTitle') . '"  href="' . route('admin.project-members.show', $row->id) . '"><i class="fa fa-plus" ></i></a>';
                return $members;
            })
            ->editColumn('project_name', function ($row) {
                $name = '<a href="' . route('admin.projects.show', $row->id) . '">' . ucfirst($row->project_name) . '</a>';

                return $name;
            })
            ->editColumn('start_date', function ($row) {
                return $row->start_date->format($this->global->date_format);
            })
            ->editColumn('deadline', function ($row) {
                if ($row->deadline) {
                    return $row->deadline->format($this->global->date_format);
                }

                return '-';
            })
            ->editColumn('client_id', function ($row) {
                if (is_null($row->client_id)) {
                    return "";
                }
                return (!is_null($row->clientdetails) && $row->clientdetails->company_name != '') ? ucwords($row->client->name) . "<br>[" . $row->clientdetails->company_name . "]" : ucwords($row->client->name);
            })
            ->editColumn('status', function ($row) {

                if ($row->status == 'in progress') {
                    $status = '<label class="label label-info">' . __('app.inProgress') . '</label>';
                } else if ($row->status == 'on hold') {
                    $status = '<label class="label label-warning">' . __('app.onHold') . '</label>';
                } else if ($row->status == 'not started') {
                    $status = '<label class="label label-warning">' . __('app.notStarted') . '</label>';
                } else if ($row->status == 'canceled') {
                    $status = '<label class="label label-danger">' . __('app.canceled') . '</label>';
                } else if ($row->status == 'finished') {
                    $status = '<label class="label label-success">' . __('app.finished') . '</label>';
                }
                return $status;
            })
            ->editColumn('completion_percent', function ($row) {
                if ($row->completion_percent < 50) {
                    $statusColor = 'danger';
                    $status = __('app.progress');
                } elseif ($row->completion_percent >= 50 && $row->completion_percent < 75) {
                    $statusColor = 'warning';
                    $status = __('app.progress');
                } else {
                    $statusColor = 'success';
                    $status = __('app.progress');

                    if ($row->completion_percent >= 100) {
                        $status = __('app.progress');
                    }
                }

                $pendingPayment = 0;
                $projectEarningTotal = Payment::join('projects', 'projects.id', '=', 'payments.project_id')
                    ->where('payments.status', 'complete')
                    ->whereNotNull('projects.project_budget')
                    ->where('payments.project_id', $row->id)
                    ->sum('payments.amount');
                $pendingPayment = ($row->project_budget - $projectEarningTotal);

                $pendingAmount = '';
                if ($pendingPayment > 0) {
                    $pendingAmount = $row->currency->currency_symbol . $pendingPayment;
                }


                $progress = '<h5>' . $status . '<span class="pull-right">' . $row->completion_percent . '%</span></h5><div class="progress">
                  <div class="progress-bar progress-bar-' . $statusColor . '" aria-valuenow="' . $row->completion_percent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $row->completion_percent . '%" role="progressbar"> <span class="sr-only">' . $row->completion_percent . '% Complete</span> </div>
                </div>';

                if ($pendingAmount != '') {
                    $progress .= '<small class="text-danger">' . __('app.unpaid') . ': ' . $pendingAmount . '</small>';
                }

                return $progress;
            })
            ->addIndexColumn()
            ->rawColumns(['project_name', 'action', 'completion_percent', 'members', 'status', 'client_id'])
            ->removeColumn('project_summary')
            ->removeColumn('notes')
            ->removeColumn('category_id')
            ->removeColumn('feedback')
            ->removeColumn('start_date');

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Project $model)
    {
        $request = $this->request();

        $model = $model->with('members', 'members.user', 'client', 'clientdetails', 'currency')->select('id', 'project_name', 'start_date', 'deadline', 'client_id', 'completion_percent', 'status', 'project_budget', 'currency_id');

        if (!is_null($request->status) && $request->status != 'all') {
            $model->where('status', $request->status);
        }

        if (!is_null($request->client_id) && $request->client_id != 'all') {
            $model->where('client_id', $request->client_id);
        }

        if (!is_null($request->team_id) && $request->team_id != 'all') {
            $model->where('team_id', $request->team_id);
        }

        if (!is_null($request->category_id) && $request->category_id != 'all') {
            $model->where('category_id', $request->category_id);
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
            ->setTableId('projects-table')
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
                   window.LaravelDataTables["projects-table"].buttons().container()
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
            '#' => ['data' => 'id', 'name' => 'id', 'visible' => true],
            __('modules.projects.projectName') => ['data' => 'project_name', 'name' => 'project_name'],
            __('modules.projects.members')  => ['data' => 'members', 'name' => 'members'],
            __('app.deadline') => ['data' => 'deadline', 'name' => 'deadline'],
            __('app.client') => ['data' => 'client_id', 'name' => 'client_id'],
            __('app.completion') => ['data' => 'completion_percent', 'name' => 'completion_percent'],
            __('app.status') => ['data' => 'status', 'name' => 'status'],
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
        return 'Projects_' . date('YmdHis');
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
