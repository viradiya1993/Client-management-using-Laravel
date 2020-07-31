<?php

namespace App\Http\Controllers\Client;

use App\Currency;
use App\Helper\Reply;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\Product;
use App\Project;
use App\Tax;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClientProductController extends ClientBaseController
{
    /**
     * MemberProductController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('app.menu.products');
        $this->pageIcon = 'icon-basket';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('client.products.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->lastInvoice    = Invoice::orderBy('id', 'desc')->first();
        $this->invoiceSetting = InvoiceSetting::first();
        $this->currencies     = Currency::all();
        $this->taxes          = Tax::all();
        $this->products       = Product::where('allow_purchase', 1)->get();

        return view('client.products.convert_product', $this->data);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $amount = $request->input('amount');
        $type = $request->input('type');
        $tax = $request->input('taxes');

        if(!$request->has('item_name')){
            return Reply::error(__('messages.selectProduct'));
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

        $invoice = new Invoice();
        $invoice->client_id = $this->user->id;
        $invoice->invoice_number = $request->invoice_number;
        $invoice->issue_date = Carbon::now()->format('Y-m-d');
        $invoice->due_date = null;
        $invoice->sub_total = round($request->sub_total,2);
        $invoice->discount = round($request->discount_value,2);
        $invoice->discount_type = 'percent';
        $invoice->total = round($request->total,2);
        $invoice->currency_id = $this->global->currency_id;
        $invoice->due_date = Carbon::now()->addDay()->format('Y-m-d');
        $invoice->note = $request->note;
        $invoice->save();

        foreach ($items as $key => $item):
            if(!is_null($item)){
                InvoiceItems::create([
                    'invoice_id' => $invoice->id,
                    'item_name' => $item,
                    'item_summary' => $itemsSummary[$key],
                    'type' => 'item',
                    'quantity' => $quantity[$key],
                    'unit_price' => round($cost_per_item[$key], 2),
                    'amount' => round($amount[$key], 2),
                    'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
                ]);
            }
        endforeach;

        return Reply::redirect(route('client.invoices.show', $invoice->id), __('messages.invoiceCreated'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       //
    }


    /**
     * @param $id
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       //
    }

    /**
     * @return mixed
     */
    public function data()
    {
        $products = Product::select('id', 'name', 'price', 'taxes')
            ->where('allow_purchase', 1)
            ->get();

        return DataTables::of($products)
            ->editColumn('name', function ($row) {
                return ucfirst($row->name);
            })
            ->editColumn('price', function ($row) {
                if (!is_null($row->taxes)) {
                    $totalTax = 0;
                    foreach (json_decode($row->taxes) as $tax){
                        $this->tax = Product::taxbyid($tax)->first();
                        $totalTax = $totalTax + ($row->price * ($this->tax->rate_percent/100));
                    }
                    return $this->global->currency->currency_symbol.($row->price+$totalTax);
                } else {
                    return $this->global->currency->currency_symbol.$row->price;
                }
            })
            ->make(true);
    }

    public function addItems(Request $request)
    {
        $this->items = Product::with('tax')->find($request->id);
        $this->taxes = Tax::all();
        $view = view('client.products.add-item', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }
}
