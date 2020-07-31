<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Invoice;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class InvoicesDataTable extends BaseDataTable
{
    protected $firstInvoice;
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $firstInvoice = $this->firstInvoice;
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($firstInvoice) {
                $action = '<div class="btn-group dropdown m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                <ul role="menu" class="dropdown-menu">
                  <li><a href="' . route("admin.all-invoices.download", $row->id) . '"><i class="fa fa-download"></i> ' . __('app.download') . '</a></li>';

                if ($row->status == 'paid') {
                    $action .= ' <li><a href="javascript:" data-invoice-id="' . $row->id . '" class="invoice-upload" data-toggle="modal" data-target="#invoiceUploadModal"><i class="fa fa-upload"></i> ' . __('app.upload') . ' </a></li>';
                }

                if ($row->status != 'paid') {
                    $action .= '<li><a href="' . route("admin.all-invoices.edit", $row->id) . '"><i class="fa fa-pencil"></i> ' . __('app.edit') . '</a></li>';
                    if (in_array('payments', $this->user->modules) && $row->credit_note == 0) {
                        $action .= '<li><a href="' . route("admin.payments.payInvoice", [$row->id]) . '" data-toggle="tooltip" ><i class="fa fa-plus"></i> ' . __('modules.payments.addPayment') . '</a></li>';
                    }
                }
                if ($firstInvoice->id == $row->id) {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="sa-params"><i class="fa fa-times"></i> ' . __('app.delete') . '</a></li>';
                }

                if ($firstInvoice->id != $row->id && $row->status == 'unpaid') {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip" title="' . __('app.cancel') . '"  data-invoice-id="' . $row->id . '" class="sa-cancel"><i class="fa fa-times"></i> ' . __('modules.invoices.markCancel') . '</a></li>';
                }

                if ($row->status != 'paid' && $row->credit_note == 0) {
                    $action .= '<li><a href="' . route("front.invoice", [md5($row->id)]) . '" target="_blank" data-toggle="tooltip" ><i class="fa fa-link"></i> ' . __('modules.payments.paymentLink') . '</a></li>';
                }
                if ($row->credit_note == 0) {
                    if ($row->status == 'paid') {
                        $action .= '<li><a href="' . route('admin.all-credit-notes.convert-invoice', $row->id) . '" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="addCreditNote"><i class="fa fa-plus"></i> ' . __('modules.credit-notes.addCreditNote') . '</a></li>';
                    } else {
                        $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="unpaidAndPartialPaidCreditNote"><i class="fa fa-plus"></i> ' . __('modules.credit-notes.addCreditNote') . '</a></li>';
                    }
                }
                if ($row->status != 'paid') {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="reminderButton"><i class="fa fa-money"></i> ' . __('app.paymentReminder') . '</a></li>';
                }

                $action .= '</ul> </div>';

                return $action;
            })
            ->editColumn('project_name', function ($row) {
                if ($row->project_id != null) {
                    return '<a href="' . route('admin.projects.show', $row->project_id) . '">' . ucfirst($row->project->project_name) . '</a>';
                }

                return '--';
            })
            ->editColumn('name', function ($row) {
                if ($row->project && $row->project->client) {
                    return ucfirst($row->project->client->name);
                } else if ($row->client_id != '') {
                    $client = User::find($row->client_id);
                    return ucfirst($client->name);
                } else if ($row->estimate && $row->estimate->client) {
                    return ucfirst($row->estimate->client->name);
                } else {
                    return '--';
                }
            })
            ->editColumn('invoice_number', function ($row) {
                return '<a href="' . route('admin.all-invoices.show', $row->id) . '">' . ucfirst($row->invoice_number) . '</a>';
            })
            ->editColumn('status', function ($row) {
                if ($row->credit_note) {
                    return '<label class="label label-warning">' . strtoupper(__('app.credit-note')) . '</label>';
                } else {
                    if ($row->status == 'unpaid') {
                        return '<label class="label label-danger">' . strtoupper($row->status) . '</label>';
                    } elseif ($row->status == 'paid') {
                        return '<label class="label label-success">' . strtoupper($row->status) . '</label>';
                    } elseif ($row->status == 'canceled') {
                        return '<label class="label label-danger">' . strtoupper(__('app.canceled')) . '</label>';
                    } else {
                        return '<label class="label label-info">' . strtoupper(__('modules.invoices.partial')) . '</label>';
                    }
                }
            })
            ->editColumn('total', function ($row) {
                $currencyCode = ' (' . $row->currency->currency_code . ') ';
                $currencySymbol = $row->currency->currency_symbol;

                return '<div class="text-right">'.__('app.total').': ' . $currencySymbol . $row->total . '<br><span class="text-success">'.__('app.paid').':</span> ' . $currencySymbol . $row->amountPaid()  . '<br><span class="text-danger">'.__('app.unpaid').':</span> ' . $currencySymbol . $row->amountDue() . '</div>';
            })
            ->editColumn(
                'issue_date',
                function ($row) {
                    return $row->issue_date->timezone($this->global->timezone)->format($this->global->date_format);
                }
            )
            ->rawColumns(['project_name', 'action', 'status', 'invoice_number', 'total'])
            ->removeColumn('currency_symbol')
            ->removeColumn('currency_code')
            ->removeColumn('project_id');

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Invoice $model)
    {
        $request = $this->request();

        $this->firstInvoice = Invoice::orderBy('id', 'desc')->first();
        $model = $model->with(['project:id,project_name,client_id', 'currency:id,currency_symbol,currency_code', 'project.client'])
            ->select('invoices.id', 'invoices.project_id', 'invoices.client_id', 'invoices.invoice_number', 'invoices.currency_id', 'invoices.total', 'invoices.status', 'invoices.issue_date', 'invoices.credit_note');

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $model = $model->where(DB::raw('DATE(invoices.`issue_date`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $model = $model->where(DB::raw('DATE(invoices.`issue_date`)'), '<=', $endDate);
        }

        if ($request->status != 'all' && !is_null($request->status)) {
            $model = $model->where('invoices.status', '=', $request->status);
        }

        if ($request->projectID != 'all' && !is_null($request->projectID)) {
            $model = $model->where('invoices.project_id', '=', $request->projectID);
        }

        if ($request->clientID != 'all' && !is_null($request->clientID)) {
            $model = $model->where('client_id', '=', $request->clientID);
        }

        $model = $model->whereHas('project', function ($q) {
            $q->whereNull('deleted_at');
        }, '>=', 0);
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
            ->setTableId('invoices-table')
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
                   window.LaravelDataTables["invoices-table"].buttons().container()
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
            __('app.invoice'). '#' => ['data' => 'invoice_number', 'name' => 'invoice_number'],
            __('app.project')  => ['data' => 'project_name', 'name' => 'project.project_name'],
            __('app.client') => ['data' => 'name', 'name' => 'project.client.name'],
            __('modules.invoices.total') => ['data' => 'total', 'name' => 'total'],
            __('modules.invoices.invoiceDate') => ['data' => 'issue_date', 'name' => 'issue_date'],
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
        return 'Invoices_' . date('YmdHis');
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
