<?php

namespace App\Http\Controllers\Admin;

use App\ClientDetails;
use App\Currency;
use App\DataTables\Admin\EstimatesDataTable;
use App\Estimate;
use App\EstimateItem;
use App\Helper\Reply;
use App\Http\Requests\StoreEstimate;
use App\InvoiceSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Tax;
use App\Product;

class ManageEstimatesController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.estimates';
        $this->pageIcon = 'ti-file';
        $this->middleware(function ($request, $next) {
            if (!in_array('estimates', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(EstimatesDataTable $dataTable)
    {
        return $dataTable->render('admin.estimates.index', $this->data);
    }

    public function create()
    {
        $this->clients = ClientDetails::all();
        $this->currencies = Currency::all();
        $this->lastEstimate = Estimate::count() + 1;
        $this->invoiceSetting = InvoiceSetting::first();
        $this->zero = '';
        if (strlen($this->lastEstimate) < $this->invoiceSetting->estimate_digit) {
            for ($i = 0; $i < $this->invoiceSetting->estimate_digit - strlen($this->lastEstimate); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }
        $this->taxes = Tax::all();
        $this->products = Product::all();
        return view('admin.estimates.create', $this->data);
    }

    public function store(StoreEstimate $request)
    {
        DB::beginTransaction();
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        $tax = $request->input('taxes');

        if (trim($items[0]) == '' || trim($items[0]) == '' || trim($cost_per_item[0]) == '') {
            return Reply::error(__('messages.addItem'));
        }

        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && (intval($qty) < 1)) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }

        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }

        $estimate = new Estimate();
        $estimate->client_id = $request->client_id;
        $estimate->estimate_number = Estimate::count() + 1;
        $estimate->valid_till = Carbon::createFromFormat($this->global->date_format, $request->valid_till)->format('Y-m-d');
        $estimate->sub_total = round($request->sub_total, 2);
        $estimate->total = round($request->total, 2);
        $estimate->currency_id = $request->currency_id;
        $estimate->note = $request->note;
        $estimate->discount = round($request->discount_value, 2);
        $estimate->discount_type = $request->discount_type;
        $estimate->status = 'waiting';
        /*dd($estimate->estimate_number);*/
        $estimate->save();

        foreach ($items as $key => $item) :
            if (!is_null($item)) {
                EstimateItem::create(
                    [
                        'estimate_id' => $estimate->id,
                        'item_name' => $item,
                        'item_summary' => $itemsSummary[$key],
                        'type' => 'item',
                        'quantity' => $quantity[$key],
                        'unit_price' => round($cost_per_item[$key], 2),
                        'amount' => round($amount[$key], 2),
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
                    ]
                );
            }
        endforeach;

        $this->logSearchEntry($estimate->id, 'Estimate #' . $estimate->id, 'admin.estimates.edit', 'estimate');
        DB::commit();

        return Reply::redirect(route('admin.estimates.index'), __('messages.estimateCreated'));
    }

    public function edit($id)
    {
        $this->estimate = Estimate::findOrFail($id);
        $this->clients = ClientDetails::all();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->products = Product::all();
        return view('admin.estimates.edit', $this->data);
    }

    public function update(StoreEstimate $request, $id)
    {
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        $tax = $request->input('taxes');


        if (trim($items[0]) == '' || trim($items[0]) == '' || trim($cost_per_item[0]) == '') {
            return Reply::error(__('messages.addItem'));
        }

        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && $qty < 1) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }

        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }


        $estimate = Estimate::findOrFail($id);
        $estimate->client_id = $request->client_id;
        $estimate->valid_till = Carbon::createFromFormat($this->global->date_format, $request->valid_till)->format('Y-m-d');
        $estimate->sub_total = round($request->sub_total, 2);
        $estimate->total = round($request->total, 2);
        $estimate->currency_id = $request->currency_id;
        $estimate->status = $request->status;
        $estimate->discount = round($request->discount_value, 2);
        $estimate->discount_type = $request->discount_type;
        $estimate->note = $request->note;
        $estimate->save();

        // delete and create new
        EstimateItem::where('estimate_id', $estimate->id)->delete();

        foreach ($items as $key => $item) :
            EstimateItem::create(
                [
                    'estimate_id' => $estimate->id,
                    'item_name' => $item,
                    'item_summary' => $itemsSummary[$key],
                    'quantity' => $quantity[$key],
                    'unit_price' => round($cost_per_item[$key], 2),
                    'amount' => round($amount[$key], 2),
                    'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
                ]
            );
        endforeach;

        return Reply::redirect(route('admin.estimates.index'), __('messages.estimateUpdated'));
    }

    public function destroy($id)
    {
        $firstEstimate = Estimate::orderBy('id', 'desc')->first();
        if ($firstEstimate->id == $id) {
            Estimate::destroy($id);
            return Reply::success(__('messages.estimateDeleted'));
        } else {
            return Reply::error(__('messages.estimateCanNotDeleted'));
        }
    }

    public function domPdfObjectForDownload($id)
    {
        $this->estimate = Estimate::findOrFail($id);
        if ($this->estimate->discount > 0) {
            if ($this->estimate->discount_type == 'percent') {
                $this->discount = (($this->estimate->discount / 100) * $this->estimate->sub_total);
            } else {
                $this->discount = $this->estimate->discount;
            }
        } else {
            $this->discount = 0;
        }
        $taxList = array();

        $items = EstimateItem::whereNotNull('taxes')
            ->where('estimate_id', $this->estimate->id)
            ->get();

        foreach ($items as $item) {
            foreach (json_decode($item->taxes) as $tax){
                $this->tax = EstimateItem::taxbyid($tax)->first();
                if ($this->tax){
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                    } else {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                    }
                }
            }
        }

        $this->taxes = $taxList;

        $this->settings = $this->global;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.estimates.estimate-pdf', $this->data);
        $filename = $this->estimate->estimate_number;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

    public function download($id)
    {
        $pdfOption = $this->domPdfObjectForDownload($id);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];

        return $pdf->download($filename . '.pdf');
    }

    public function export($startDate, $endDate, $status)
    {
        $invoices = Estimate::with(['client:id,name', 'currency:id,currency_symbol']);

        if ($startDate !== null && $startDate != 'null' && $startDate != '') {
            $invoices = $invoices->where(DB::raw('DATE(estimates.`valid_till`)'), '>=', $startDate);
        }

        if ($endDate !== null && $endDate != 'null' && $endDate != '') {
            $invoices = $invoices->where(DB::raw('DATE(estimates.`valid_till`)'), '<=', $endDate);
        }

        if ($status != 'all' && !is_null($status)) {
            $invoices = $invoices->where('estimates.status', '=', $status);
        }

        $invoices = $invoices->orderBy('id', 'desc')
        ->get()
        ->map(function($invoice) {
            return [
                'id' => $invoice->id,
                'estimate_number' => $invoice->original_estimate_number,
                'client' => $invoice->client->name,
                'status' => $invoice->status,
                'total' => $invoice->currency->currency_symbol.$invoice->total,
                'valid_date' => $invoice->valid_date
            ];
        })->toArray();

        $headerRow = ['ID', 'Estimate Number', 'Client', 'Status', 'Total', 'Valid Date'];

        array_unshift($invoices, $headerRow);

        // Generate and return the spreadsheet
        Excel::create('estimate', function ($excel) use ($invoices) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Estimate');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('estimate file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($invoices) {
                $sheet->fromArray($invoices, null, 'A1', false, false);

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
