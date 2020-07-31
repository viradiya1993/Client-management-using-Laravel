<?php

namespace App\DataTables\Admin;

use App\CreditNotes;
use App\DataTables\BaseDataTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class AllCreditNotesDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    protected $firstCreditNotes;
    public function dataTable($query)
    {
        $firstCreditNotes = $this->firstCreditNotes;

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use($firstCreditNotes){
                $action = '<div class="btn-group m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">'.__('app.action').' <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu">
                    <li><a href="' . route("admin.all-credit-notes.download", $row->id) . '"><i class="fa fa-download"></i> '.__('app.download').'</a></li>';

                    $action .= ' <li><a href="javascript:" data-credit-notes-id="' . $row->id . '" class="credit-notes-upload" data-toggle="modal" data-target="#creditNoteUploadModal"><i class="fa fa-upload"></i> '.__('app.upload').' </a></li>';
                if ($row->status == 'open') {
                    $action .= '<li><a href="' . route("admin.all-credit-notes.edit", $row->id) . '"><i class="fa fa-pencil"></i> '.__('app.edit').'</a></li>';
                }
                if($firstCreditNotes->id == $row->id){
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-credit-notes-id="' . $row->id . '" class="sa-params"><i class="fa fa-times"></i> '.__('app.delete').'</a></li>';
                }
                $action .= '</ul>
              </div>';
                return $action;
            })
            ->editColumn('project_name', function ($row) {
                if($row->project_id){
                    return '<a href="' . route('admin.projects.show', $row->project_id) . '">' . ucfirst($row->project->project_name) . '</a>';
                }
                return '--';
            })
            ->editColumn('cn_number', function ($row) {
                return '<a href="' . route('admin.all-credit-notes.show', $row->id) . '">' . ucfirst($row->cn_number) . '</a>';
            })
            ->editColumn('invoice_number', function ($row) {
                return $row->invoice ? ucfirst($row->invoice->invoice_number) : '--';
            })
            ->editColumn('total', function ($row) {
                $currencyCode = ' (' . $row->currency->currency_code . ') ';
                $currencySymbol = $row->currency->currency_symbol;

                return '<div class="text-right">Total: '.$currencySymbol.$row->total.$currencyCode.'<br>Used: '.$currencySymbol.$row->creditAmountUsed().$currencyCode.'<br>Remaining: '.$currencySymbol.$row->creditAmountRemaining().$currencyCode.'</div>';
            })
            ->editColumn(
                'issue_date',
                function ($row) {
                    return $row->issue_date->timezone($this->global->timezone)->format($this->global->date_format);
                }
            )
            ->editColumn('status', function ($row) {
                if ($row->status == 'open') {
                    return '<label class="label label-success">' . strtoupper($row->status) . '</label>';
                }
                else {
                    return '<label class="label label-danger">' . strtoupper($row->status) . '</label>';
                }
            })
            ->rawColumns(['project_name', 'action', 'cn_number', 'invoice_number', 'status', 'total'])
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
    public function query(CreditNotes $model)
    {
        $request = $this->request();

        $this->firstCreditNotes = CreditNotes::orderBy('id', 'desc')->first();

        $model = $model->with(['project:id,project_name,client_id', 'currency:id,currency_symbol,currency_code', 'invoice'])
            ->select('id', 'project_id', 'invoice_id', 'currency_id', 'cn_number', 'total', 'issue_date', 'status');

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $model = $model->where(DB::raw('DATE(credit_notes.`issue_date`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $model = $model->where(DB::raw('DATE(credit_notes.`issue_date`)'), '<=', $endDate);
        }

        if ($request->projectID != 'all' && !is_null($request->projectID)) {
            $model = $model->where('credit_notes.project_id', '=', $request->projectID);
        }

        $model = $model->orderBy('credit_notes.id', 'desc');

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
            ->setTableId('allCreditNote-table')
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
                   window.LaravelDataTables["allCreditNote-table"].buttons().container()
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
            __('app.credit-note') => ['data' => 'cn_number', 'name' => 'cn_number'],
            __('app.invoice')  => ['data' => 'invoice_number', 'name' => 'invoice.invoice_number'],
            __('app.project') => ['data' => 'project_name', 'name' => 'project.project_name'],
            __('modules.credit-notes.total') => ['data' => 'total', 'name' => 'total'],
            __('modules.credit-notes.creditNoteDate') => ['data' => 'issue_date', 'name' => 'issue_date'],
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
        return 'All_credit_notes' . date('YmdHis');
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
