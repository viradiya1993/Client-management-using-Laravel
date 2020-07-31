<?php

namespace App\DataTables\Admin;

use App\ClientDetails;
use App\DataTables\BaseDataTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class ClientsDataTable extends BaseDataTable
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
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                <ul role="menu" class="dropdown-menu pull-right">
                  <li><a href="' . route('admin.clients.edit', [$row->id]) . '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . trans('app.edit') . '</a></li>
                  <li><a href="' . route('admin.clients.projects', [$row->user_id]) . '"><i class="fa fa-search" aria-hidden="true"></i> ' . __('app.view') . '</a></li>
                  <li><a href="javascript:;"  data-user-id="' . $row->user_id . '"  class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> ' . trans('app.delete') . '</a></li>';

                $action .= '</ul> </div>';

                return $action;

            })
            ->editColumn(
                'name',
                function ($row) {
                    return '<a href="' . route('admin.clients.projects', $row->user_id) . '">' . ucfirst($row->name) . '</a>';
                }
            )
            ->editColumn(
                'created_at',
                function ($row) {
                    return Carbon::parse($row->created_at)->format($this->global->date_format);
                }
            )
            ->editColumn(
                'status',
                function ($row) {
                    if($row->status == 'active'){
                        return '<label class="label label-success">'.__('app.active').'</label>';
                    } else{
                        return '<label class="label label-danger">'.__('app.inactive').'</label>';
                    }
                }
            )
            ->addIndexColumn()
            ->rawColumns(['name', 'action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ClientDetails $model)
    {
        $request = $this->request();
        
        $model = $model->join('users', 'client_details.user_id', '=', 'users.id')
            ->select('client_details.id', 'client_details.user_id', 'client_details.name', 'client_details.company_name', 'client_details.email', 'client_details.created_at', 'users.status')
            ->groupBy('client_details.id');

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $model = $model->where(DB::raw('DATE(users.`created_at`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $model = $model->where(DB::raw('DATE(users.`created_at`)'), '<=', $endDate);
        }
        if ($request->status != 'all' && $request->status != '') {
            $model = $model->where('users.status', $request->status);
        }

        if ($request->client != 'all' && $request->client != '') {
            $model = $model->where('users.id', $request->client);
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
            ->setTableId('clients-table')
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
                   window.LaravelDataTables["clients-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
            ])
            ->buttons(Button::make(['extend'=> 'export','buttons' => ['excel', 'csv']]));
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
            __('app.name') => ['data' => 'name', 'name' => 'name'],
            __('modules.client.companyName') => ['data' => 'company_name', 'name' => 'client_details.company_name'],
            __('app.email') => ['data' => 'email', 'name' => 'email'],
            __('app.status') => ['data' => 'status', 'name' => 'status'],
            __('app.createdAt') => ['data' => 'created_at', 'name' => 'created_at'],
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
        return 'clients_' . date('YmdHis');
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
