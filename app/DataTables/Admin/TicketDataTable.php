<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Ticket;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class TicketDataTable extends BaseDataTable
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
                return '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                <ul role="menu" class="dropdown-menu">
                  <li><a href="' . route("admin.tickets.edit", $row->id) . '" ><i class="fa fa-eye"></i> '.__('app.view').'</a></li>
                  <li><a href="javascript:;" class="sa-params" data-ticket-id="' . $row->id . '"><i class="fa fa-times"></i> Delete</a></li>
                </ul>
              </div>
              ';
            })
            ->addColumn('others', function ($row) {
                $others = '<ul style="list-style: none; padding: 0; ">
                    <li>' . __('modules.tickets.agent') . ': ' . (is_null($row->agent_id) ? "-" : ucwords($row->agent->name)) . '</li>';
                if ($row->status == 'open') {
                    $others .= '<li>' . __('app.status') . ': <label class="label label-danger">' . $row->status . '</label></li>';
                } elseif ($row->status == 'pending') {
                    $others .= '<li>' . __('app.status') . ': <label class="label label-warning">' . $row->status . '</label></li>';
                } elseif ($row->status == 'resolved') {
                    $others .= '<li>' . __('app.status') . ': <label class="label label-info">' . $row->status . '</label></li>';
                } elseif ($row->status == 'closed') {
                    $others .= '<li>' . __('app.status') . ': <label class="label label-success">' . $row->status . '</label></li>';
                }
                $others .= '<li>' . __('modules.tasks.priority') . ': ' . $row->priority . '</li>
                </ul>';
                return $others;
            })
            ->editColumn('subject', function ($row) {
                return ucfirst($row->subject);
            })
            ->editColumn('user_id', function ($row) {
                return ucwords($row->requester->name);
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->timezone($this->global->timezone)->format($this->global->date_format . ' ' . $this->global->time_format);
            })
            ->rawColumns(['others', 'action'])
            ->removeColumn('agent_id')
            ->removeColumn('channel_id')
            ->removeColumn('type_id')
            ->removeColumn('updated_at')
            ->removeColumn('deleted_at');

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Ticket $model)
    {
        $request = $this->request();
        $model = $model->select('tickets.*');
        if ($request->startDate != 0) {
            $model->where(DB::raw('DATE(tickets.updated_at)'), '>=', $request->startDate);
        }

        if ($request->endDate != 0) {
            $model->where(DB::raw('DATE(tickets.updated_at)'), '<=', $request->endDate);
        }

        if ($request->agentId != 0) {
            $model->where('tickets.agent_id', '=', $request->agentId);
        }

        if ($request->status) {
            $model->where('tickets.status', '=', $request->status);
        }

        if ($request->priority) {
            $model->where('tickets.priority', '=', $request->priority);
        }

        if ($request->channelId != 0) {
            $model->where('tickets.channel_id', '=', $request->channelId);
        }

        if ($request->typeId != 0) {
            $model->where('tickets.type_id', '=', $request->typeId);
        }

        if ($request->tagId != 0) {
            $model->join('ticket_tags', 'ticket_tags.ticket_id', 'tickets.id');
            $model->where('ticket_tags.tag_id', '=', $request->tagId);
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
            ->setTableId('ticket-table')
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
                   window.LaravelDataTables["ticket-table"].buttons().container()
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
            // __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false ],
            // __('modules.tickets.ticket').' #' => ['data' => 'id', 'name' => 'id' ],
            __('modules.tickets.ticketSubject')  => ['data' => 'subject', 'name' => 'subject'],
            __('modules.tickets.requesterName') => ['data' => 'user_id', 'name' => 'user_id'],
            __('modules.tickets.requestedOn') => ['data' => 'created_at', 'name' => 'created_at'],
            __('app.others') => ['data' => 'others', 'name' => 'others'],
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
        return 'Tickets_' . date('YmdHis');
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
