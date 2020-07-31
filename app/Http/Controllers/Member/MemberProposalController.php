<?php

namespace App\Http\Controllers\Member;

use App\Currency;
use App\Helper\Reply;
use App\Http\Requests\Proposal\StoreRequest;
use App\Invoice;
use App\InvoiceSetting;
use App\Lead;
use App\Proposal;
use App\ProposalItem;
use App\Tax;
use App\User;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class MemberProposalController extends MemberBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageIcon = 'user-follow';
        $this->pageTitle = 'proposal';


        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            if(!in_array('leads',$this->user->modules)){
                abort(403);
            }
            if(!$this->user->can('edit_lead')){
                abort(403);
            }
            return $next($request);
        });

    }

    public function Index() {

        $this->totalProposals = Proposal::count();
        return view('member.proposals.index', $this->data);
    }

    public function show($id) {

        $this->lead = Lead::where('id', $id)->first();
        return view('member.proposals.show', $this->data);
    }

    public function data($id = null) {
        $lead = Proposal::select('proposals.id','leads.company_name','total','valid_till','status','leads.client_id', 'currencies.currency_symbol')
            ->join('currencies', 'currencies.id', '=', 'proposals.currency_id')
            ->join('leads', 'leads.id', 'proposals.lead_id');

        if($id){
            $lead = $lead->where('proposals.lead_id', $id);
        }
        $lead = $lead->get();

        return DataTables::of($lead)
            ->addColumn('action', function ($row) {
                $convert = '';

                if($row->status == 'waiting'){
                    $convert = '<li><a href="' . route("member.proposals.convert-proposal", $row->id) . '" ><i class="fa fa-file"></i> Convert Invoice </a></li>';
                }
                return '<div class="btn-group m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu">
                  <li><a href="' . route("member.proposals.download", $row->id) . '" ><i class="fa fa-download"></i> Download</a></li>
                  <li><a href="' . route("member.proposals.edit", $row->id) . '" ><i class="fa fa-pencil"></i> Edit</a></li>
                   '.$convert.'
                  <li><a class="sa-params" href="javascript:;" data-proposal-id="' . $row->id . '"><i class="fa fa-times"></i> Delete</a></li>
                </ul>
              </div>';
            })
            ->editColumn('name', function ($row) {
                if($row->client_id){
                    return '<a href="' . route('member.clients.projects', $row->client_id) . '">' . ucwords($row->name) . '</a>';
                }
                return ucwords($row->name);
            })
            ->editColumn('status', function ($row) {
                if($row->status == 'waiting'){
                    return '<label class="label label-warning">'.strtoupper($row->status).'</label>';
                }
                if($row->status == 'declined'){
                    return '<label class="label label-danger">'.strtoupper($row->status).'</label>';
                }else{
                    return '<label class="label label-success">'.strtoupper($row->status).'</label>';
                }
            })
            ->editColumn('total', function ($row) {
                return $row->currency_symbol . $row->total;
            })
            ->editColumn(
                'valid_till',
                function ($row) {
                    return Carbon::parse($row->valid_till)->format($this->global->date_format);
                }
            )
            ->rawColumns(['name', 'action', 'status'])
            ->removeColumn('currency_symbol')
            ->removeColumn('client_id')
            ->removeColumn('client_id')
            ->make(true);
    }

    public function create($leadID = null) {
        $this->leads = Lead::all();
        $this->taxes = Tax::all();

        if($leadID){
            $this->lead = Lead::findOrFail($leadID);
        }

        $this->currencies = Currency::all();
        return view('member.proposals.create', $this->data);
    }

    public function store(StoreRequest $request)
    {
        $items = $request->item_name;
        $cost_per_item = $request->cost_per_item;
        $quantity = $request->quantity;
        $amount = $request->amount;
        $itemsSummary = $request->input('item_summary');
        $tax = $request->input('taxes');
        $type = $request->type;


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

        $proposal = new Proposal();
        $proposal->lead_id = $request->lead_id;
        $proposal->valid_till = Carbon::createFromFormat($this->global->date_format, $request->valid_till)->format('Y-m-d');
        $proposal->sub_total = $request->sub_total;
        $proposal->total = $request->total;
        $proposal->currency_id = $request->currency_id;
        $proposal->note = $request->note;
        $proposal->discount = round($request->discount_value, 2);
        $proposal->discount_type = $request->discount_type;
        $proposal->status = 'waiting';
        $proposal->save();

        foreach ($items as $key => $item):
            if (!is_null($item)) {
                ProposalItem::create(
                    [
                        'proposal_id' => $proposal->id,
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

        // Notify client
//        $notifyUser = User::withoutGlobalScope('active')->findOrFail($proposal->client_id);
//        $notifyUser->notify(new NewProposal($proposal));

        $this->logSearchEntry($proposal->id, 'Proposal #'.$proposal->id, 'member.proposals.edit', 'proposal');

        return Reply::redirect(route('member.proposals.show', $proposal->lead_id), __('messages.proposalCreated'));

    }

    public function edit($id) {
        $this->Leads = Lead::all();
        $this->currencies = Currency::all();
        $this->perposal = Proposal::findOrFail($id);
        return view('member.proposals.edit', $this->data);
    }

    public function update(StoreRequest $request, $id)
    {
        $items = $request->item_name;
        $cost_per_item = $request->cost_per_item;
        $quantity = $request->quantity;
        $amount = $request->amount;
        $type = $request->type;
        $itemsSummary = $request->input('item_summary');
        $tax = $request->input('taxes');

        if (trim($items[0]) == '' || trim($items[0]) == '' || trim($cost_per_item[0]) == '') {
            return Reply::error(__('messages.addItem'));
        }

        foreach ($quantity as $qty) {
            if (!is_numeric($qty)) {
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

        $proposal = Proposal::findOrFail($id);
        $proposal->lead_id = $request->lead_id;
        $proposal->valid_till = Carbon::createFromFormat($this->global->date_format, $request->valid_till)->format('Y-m-d');
        $proposal->sub_total = $request->sub_total;
        $proposal->total = $request->total;
        $proposal->currency_id = $request->currency_id;
        $proposal->status = $request->status;
        $proposal->note = $request->note;
        $proposal->discount = round($request->discount_value, 2);
        $proposal->discount_type = $request->discount_type;
        $proposal->save();

        // delete and create new
        ProposalItem::where('proposal_id', $proposal->id)->delete();

        foreach ($items as $key => $item):
            if (!is_null($item)) {
                ProposalItem::create(
                    [
                        'proposal_id' => $proposal->id,
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


        // Notify client
//        $notifyUser = User::withoutGlobalScope('active')->findOrFail($proposal->client_id);
//        $notifyUser->notify(new NewProposal($proposal));

        return Reply::redirect(route('member.proposals.show', $proposal->id), __('messages.proposalUpdated'));

    }

    public function destroy($id) {
        Proposal::destroy($id);
        return Reply::success(__('messages.proposalDeleted'));
    }

    public function download($id) {
        $this->proposal = Proposal::findOrFail($id);
        $this->discount = ProposalItem::where('type', 'discount')
            ->where('proposal_id', $this->proposal->id)
            ->sum('amount');
        $this->taxes = ProposalItem::where('type', 'tax')
            ->where('proposal_id', $this->proposal->id)
            ->get();

        $this->settings = $this->global;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('member.proposals.proposal-pdf', $this->data);
        $filename = 'proposal-'.$this->proposal->id;

        return $pdf->download($filename . '.pdf');
    }

    public function convertProposal($id)
    {
        $this->proposalId = $id;
        $this->invoice = Proposal::with('items','lead','lead.client')->findOrFail($id);
        $this->lastInvoice = Invoice::count() + 1;
        $this->invoiceSetting = InvoiceSetting::first();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->clients = User::allClients();
        $this->zero = '';

        if(!is_null($this->invoice->client_id)){
            $this->clientDetail = User::findOrFail($this->invoice->client_id);
        }

        if (strlen($this->lastInvoice) < $this->invoiceSetting->invoice_digit) {
            for ($i = 0; $i < $this->invoiceSetting->invoice_digit - strlen($this->lastInvoice); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }
        //        foreach ($this->invoice->items as $items)

        $discount = $this->invoice->items->filter(function ($value, $key) {
            return $value->type == 'discount';
        });

        $tax = $this->invoice->items->filter(function ($value, $key) {
            return $value->type == 'tax';
        });

        $this->totalTax = $tax->sum('amount');
        $this->totalDiscount = $discount->sum('amount');

        return view('member.proposals.convert_proposal', $this->data);
    }
}