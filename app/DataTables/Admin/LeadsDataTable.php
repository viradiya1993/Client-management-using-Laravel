<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Lead;
use App\LeadStatus;
use App\User;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class LeadsDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $currentDate = Carbon::today()->format('Y-m-d');
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($row) {

                if ($row->client_id == null || $row->client_id == '') {
                    $follow = '<li><a href="' . route('admin.clients.create') . '/' . $row->id . '"><i class="fa fa-user"></i> ' . __('modules.lead.changeToClient') . '</a></li>';
                    if ($row->next_follow_up == 'yes') {
                        $follow .= '<li onclick="followUp(' . $row->id . ')"><a href="javascript:;"><i class="fa fa-thumbs-up"></i> ' . __('modules.lead.addFollowUp') . '</a></li>';
                    }
                } else {
                    $follow = '';
                }
                $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                <ul role="menu" class="dropdown-menu pull-right">
                    <li><a href="' . route('admin.leads.show', $row->id) . '"><i class="fa fa-search"></i> ' . __('modules.lead.view') . '</a></li>
                    <li><a href="' . route('admin.leads.edit', $row->id) . '"><i class="fa fa-edit"></i> ' . __('modules.lead.edit') . '</a></li>
                    <li><a href="javascript:;" class="sa-params" data-user-id="' . $row->id . '"><i class="fa fa-trash "></i> ' . __('app.delete') . '</a></li>
                     ' . $follow . '   
                </ul>
              </div>';
                return $action;
            })
            ->addColumn('status', function ($row) {
                $status = LeadStatus::all();
                $statusLi = '';
                foreach ($status as $st) {
                    if ($row->status_id == $st->id) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $statusLi .= '<option ' . $selected . ' value="' . $st->id . '">' . $st->type . '</option>';
                }

                $action = '<select class="form-control" name="statusChange" onchange="changeStatus( ' . $row->id . ', this.value)">
                    ' . $statusLi . '
                </select>';


                return $action;
            })
            ->editColumn('client_name', function ($row) {
                if ($row->client_id != null && $row->client_id != '') {
                    $label = '<label class="label label-success">' . __('app.client') . '</label>';
                } else {
                    $label = '<label class="label label-info">' . __('app.lead') . '</label>';
                }

                return '<a href="' . route('admin.leads.show', $row->id) . '">' . $row->client_name . '</a><div class="clearfix"></div> ' . $label;
            })
            ->editColumn('next_follow_up_date', function ($row) use ($currentDate) {
                if ($row->next_follow_up_date != null && $row->next_follow_up_date != '') {
                    $date = Carbon::parse($row->next_follow_up_date)->format($this->global->date_format);
                } else {
                    $date = '--';
                }
                if ($row->next_follow_up_date < $currentDate && $date != '--') {
                    return $date . ' <label class="label label-danger">' . __('app.pending') . '</label>';
                }

                return $date;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format($this->global->date_format);
            })
            ->removeColumn('status_id')
            ->removeColumn('client_id')
            ->removeColumn('source')
            ->removeColumn('next_follow_up')
            ->removeColumn('statusName')
            ->rawColumns(['status', 'action', 'client_name', 'next_follow_up_date']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $currentDate = Carbon::now()->format('Y-m-d');
        $lead = Lead::select('leads.id', 'leads.client_id', 'leads.next_follow_up', 'client_name', 'company_name', 'lead_status.type as statusName', 'status_id', 'leads.created_at', 'lead_sources.type as source', \DB::raw("(select next_follow_up_date from lead_follow_up where lead_id = leads.id and leads.next_follow_up  = 'yes' and DATE(next_follow_up_date) >= {$currentDate} ORDER BY next_follow_up_date asc limit 1) as next_follow_up_date"))
            ->leftJoin('lead_status', 'lead_status.id', 'leads.status_id')
            ->leftJoin('lead_sources', 'lead_sources.id', 'leads.source_id');
        if ($this->request()->followUp != 'all' && $this->request()->followUp != '') {
            $lead = $lead->leftJoin('lead_follow_up', 'lead_follow_up.lead_id', 'leads.id');
            if ($this->request()->followUp == 'yes') {
                $lead = $lead->where('leads.next_follow_up', 'yes');
            } else {
                $lead = $lead->where('leads.next_follow_up', 'no');
            }
            // $lead = $lead->where('lead_follow_up.next_follow_up_date', '<', $currentDate);
        }
        if ($this->request()->client != 'all' && $this->request()->client != '') {
            if ($this->request()->client == 'lead') {
                $lead = $lead->whereNull('client_id');
            } else {
                $lead = $lead->whereNotNull('client_id');
            }
        }

        return $lead->GroupBy('leads.id');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('leads-table')
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
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["leads-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
            ])
            ->buttons(
                Button::make(['extend'=> 'export','buttons' => ['excel', 'csv']])
            );
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
            __('app.clientName') => ['data' => 'client_name', 'name' => 'client_name'],
            __('modules.lead.companyName') => ['data' => 'company_name', 'name' => 'company_name'],
            __('app.createdOn') => ['data' => 'created_at', 'name' => 'created_at'],
            __('modules.lead.nextFollowUp') => ['data' => 'next_follow_up_date', 'name' => 'next_follow_up_date', 'orderable' => false, 'searchable' => false],
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
        return 'leads_' . date('YmdHis');
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
