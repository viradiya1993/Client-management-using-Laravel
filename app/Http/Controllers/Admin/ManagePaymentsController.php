<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\DataTables\Admin\PaymentsDataTable;
use App\Helper\Reply;
use App\Http\Requests\Payments\ImportPayment;
use App\Http\Requests\Payments\StorePayment;
use App\Http\Requests\Payments\UpdatePayments;
use App\Invoice;
use App\Payment;
use App\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ManagePaymentsController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.payments';
        $this->pageIcon = 'fa fa-money';
        $this->middleware(function ($request, $next) {
            if(!in_array('payments',$this->user->modules)){
                abort(403);
            }
            return $next($request);
        });

    }

    public function index(PaymentsDataTable $dataTable) {
        $this->projects = Project::all();
        return $dataTable->render('admin.payments.index', $this->data);
    }

    public function create(){
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        return view('admin.payments.create', $this->data);
    }

    public function store(StorePayment $request)
    {
        $payment = new Payment();
        if($request->has('invoice_id') ){
            $invoice = Invoice::findOrFail($request->invoice_id);
            $payment->project_id = $invoice->project_id;
            $payment->invoice_id = $invoice->id;
            $payment->currency_id = $invoice->currency->id;
            $paidAmount = $invoice->amountPaid();
        }

        else if($request->project_id != ''){
            $payment->project_id = $request->project_id;
            $payment->currency_id = $request->currency_id;
        }
        else{
            $currency = Currency::first();
            $payment->currency_id = $currency->id;
        }

        $payment->amount = round($request->amount, 2);
        $payment->gateway = $request->gateway;
        $payment->transaction_id = $request->transaction_id;
        $payment->paid_on =  Carbon::createFromFormat('d/m/Y H:i', $request->paid_on)->format('Y-m-d H:i:s');

        $payment->remarks = $request->remarks;
        $payment->save();

        if($request->has('invoice_id') ){

            if(($paidAmount+$request->amount) >= $invoice->total){
                $invoice->status = 'paid';
            }
            else{
                $invoice->status = 'partial';
            }
            $invoice->save();
        }



        return Reply::redirect(route('admin.payments.index'), __('messages.paymentSuccess'));
    }

    public function destroy($id) {
        $payment = Payment::find($id);

        // change invoice status if exists
        if ($payment->invoice) {
            $due = $payment->invoice->amountDue() + $payment->amount;
            if ($due <= 0) {
                $payment->invoice->status = 'paid';
            }
            else if ($due >= $payment->invoice->total) {
                $payment->invoice->status = 'unpaid';
            }
            else {
                $payment->invoice->status = 'partial';
            }
            $payment->invoice->save();
        }

        $payment->delete();

        return Reply::success(__('messages.paymentDeleted'));
    }

    public function edit($id){
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        $this->payment = Payment::findOrFail($id);
        return view('admin.payments.edit', $this->data);
    }

    public function update(UpdatePayments $request, $id){

        $payment = Payment::findOrFail($id);
        if($request->project_id != ''){
            $payment->project_id = $request->project_id;
        }
        $payment->currency_id = $request->currency_id;
        $payment->amount = round($request->amount, 2);
        $payment->gateway = $request->gateway;
        $payment->transaction_id = $request->transaction_id;
        $payment->paid_on = Carbon::createFromFormat('d/m/Y H:i', $request->paid_on)->format('Y-m-d H:i:s');
        $payment->status = $request->status;
        $payment->remarks = $request->remarks;
        $payment->save();

        // change invoice status if exists
        if ($payment->invoice) {
            if ($payment->invoice->amountDue() <= 0) {
                $payment->invoice->status = 'paid';
            }
            else if ($payment->invoice->amountDue() >= $payment->invoice->total) {
                $payment->invoice->status = 'unpaid';
            }
            else {
                $payment->invoice->status = 'partial';
            }
            $payment->invoice->save();
        }

        return Reply::redirect(route('admin.payments.index'), __('messages.paymentSuccess'));
    }

    public function payInvoice($invoiceId){
        $this->invoice = Invoice::findOrFail($invoiceId);
        $this->paidAmount = $this->invoice->amountPaid();


        if($this->invoice->status == 'paid'){
            return "Invoice already paid";
        }

        return view('admin.payments.pay-invoice', $this->data);
    }

    public function importExcel(ImportPayment $request){
        if($request->hasFile('import_file')){
            $path = $request->file('import_file')->getRealPath();
            $data = Excel::load($path)->get();

            if($data->count()){

                foreach ($data as $key => $value) {

                    if($request->currency_character){
                        $amount = substr($value->amount, 1);
                    }
                    else{
                        $amount = substr($value->amount, 0);
                    }

                    $amount = str_replace( ',', '', $amount );
                    $amount = str_replace( ' ', '', $amount );

                    $arr[] = [
                        'paid_on' => Carbon::parse($value->date)->format('Y-m-d'),
                        'amount' => $amount,
                        'currency_id' => $this->global->currency_id,
                        'status' => 'complete'
                    ];
                }

                if(!empty($arr)){
                    DB::table('payments')->insert($arr);
                }
            }
        }

        return Reply::redirect(route('admin.payments.index'), __('messages.importSuccess'));
    }

    public function downloadSample(){
        return response()->download(public_path().'/sample/payment-sample.csv');
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $status
     */
    public function export($startDate, $endDate, $status, $project) {

        $payments = Payment::with(['project:id,project_name', 'currency:id,currency_code,currency_symbol','invoice:id,invoice_number']);

        if($startDate !== null && $startDate != 'null' && $startDate != ''){
            $payments = $payments->where(DB::raw('DATE(payments.`paid_on`)'), '>=', $startDate);
        }

        if($endDate !== null && $endDate != 'null' && $endDate != ''){
            $payments = $payments->where(DB::raw('DATE(payments.`paid_on`)'), '<=', $endDate);
        }

        if($status != 'all' && !is_null($status)){
            $payments = $payments->where('payments.status', '=', $status);
        }

        if($project != 'all' && !is_null($project)){
            $payments = $payments->where('payments.project_id', '=', $project);
        }

        $payments = $payments->orderBy('id', 'desc')
        ->get()
        ->map(function($payment) {
            return [
                'id' => $payment->id,
                'project_name' => $payment->project ? $payment->project->project_name : '--',
                'invoice_number' => $payment->invoice ? $payment->invoice->original_invoice_number : '--',
                'currency_code' => $payment->currency->currency_code,
                'status' => $payment->status,
                'remark' => $payment->remark,
                'amount' => $payment->amount,
                'paid_on' => $payment->paid_on
            ];
        })->toArray();

        $headerRow = ['ID', 'Estimate Number', 'Client', 'Status', 'Total', 'Valid Date'];


        $headerRow = ['ID','Project','Invoice #', 'Currency Code','Status','Remark','Amount', 'Paid On'];

        array_unshift($payments, $headerRow);

        // Generate and return the spreadsheet
        Excel::create('payment', function($excel) use ($payments) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Payment');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('payment file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($payments) {
                $sheet->fromArray($payments, null, 'A1', false, false);

                $sheet->row(1, function($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold'       =>  true
                    ));

                });

            });

        })->download('xlsx');
    }

}
