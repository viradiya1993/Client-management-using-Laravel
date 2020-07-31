<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Company;
use App\GlobalSetting;
use App\Helper\Reply;

use App\Http\Requests\SuperAdmin\Companies\DeleteRequest;
use App\Invoice;
use App\OfflineInvoice;
use App\RazorpayInvoice;
use App\Traits\StripeSettings;
use App\PaypalInvoice;
use App\StripeInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SuperAdminInvoiceController extends SuperAdminBaseController
{
    use StripeSettings;
    /**
     * SuperAdminInvoiceController constructor.
     */
    public function __construct() {

        parent::__construct();
        $this->pageTitle = 'Invoices';
        $this->pageIcon = 'icon-layers';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stripeInvoices = DB::table("stripe_invoices")
            ->whereNotNull('stripe_invoices.pay_date')->count();

        $razorpayInvoice = DB::table("razorpay_invoices")
            ->whereNotNull('razorpay_invoices.pay_date')->count();

        $PaypalInvoices = DB::table("paypal_invoices")
            ->where('paypal_invoices.status', 'paid')->count();

        $this->totalInvoices = ($stripeInvoices + $PaypalInvoices + $razorpayInvoice);
        return view('super-admin.invoices.index', $this->data);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteRequest $request, $id)
    {
        Company::destroy($id);
        return Reply::success('Company deleted successfully.');
    }

    /**
     * @return mixed
     */
    public function data()
    {
        $stripe = DB::table("stripe_invoices")
            ->join('packages', 'packages.id', 'stripe_invoices.package_id')
            ->join('companies', 'companies.id', 'stripe_invoices.company_id')
            ->selectRaw('stripe_invoices.id, stripe_invoices.invoice_id ,companies.company_name as company, 
            packages.name as package, stripe_invoices.transaction_id, "Stripe" as method,stripe_invoices.amount, 
            stripe_invoices.pay_date as paid_on ,stripe_invoices.next_pay_date,"" as offline_method_id')
            ->whereNotNull('stripe_invoices.pay_date');

        $razorpay = DB::table("razorpay_invoices")
            ->join('packages', 'packages.id', 'razorpay_invoices.package_id')
            ->join('companies', 'companies.id', 'razorpay_invoices.company_id')
            ->selectRaw('razorpay_invoices.id ,razorpay_invoices.invoice_id , companies.company_name as company,
             packages.name as name, razorpay_invoices.transaction_id, "Razorpay" as method,razorpay_invoices.amount, razorpay_invoices.pay_date as paid_on ,
             razorpay_invoices.next_pay_date,"" as offline_method_id')
            ->whereNotNull('razorpay_invoices.pay_date');

        $paypal = DB::table("paypal_invoices")
            ->join('packages', 'packages.id', 'paypal_invoices.package_id')
            ->join('companies', 'companies.id', 'paypal_invoices.company_id')
            ->selectRaw('paypal_invoices.id,"" as invoice_id, companies.company_name as company, 
                packages.name as package, paypal_invoices.transaction_id,
             "Paypal" as method , paypal_invoices.total as amount, paypal_invoices.paid_on,
             paypal_invoices.next_pay_date,"" as offline_method_id')
            ->where('paypal_invoices.status', 'paid');

        $offline = OfflineInvoice::join('packages', 'packages.id', 'offline_invoices.package_id')
            ->join('companies', 'companies.id', 'offline_invoices.company_id')
            ->selectRaw('offline_invoices.id,"" as invoice_id,companies.company_name as company,
             packages.name as package, offline_invoices.transaction_id,
              "Offline" as method ,offline_invoices.amount as amount, offline_invoices.pay_date as paid_on,
              offline_invoices.next_pay_date,offline_invoices.offline_method_id')
            ->with('offline_payment_method')
            ->union($paypal)
            ->union($stripe)
            ->union($razorpay)
            ->get()->sortByDesc('paid_on');


        return Datatables::of($offline)

            ->editColumn('company', function ($row) {
                    return ucfirst($row->company);
            })
            ->editColumn('package', function ($row) {
                return ucfirst($row->package);
            })
            ->editColumn('paid_on', function ($row) {
                if(!is_null($row->paid_on)) {
                    return Carbon::parse($row->paid_on)->format('d-m-Y');
                }
                return '-';
            })
            ->editColumn('next_pay_date', function ($row) {
                if(!is_null($row->next_pay_date)) {
                    return Carbon::parse($row->next_pay_date)->format('d-m-Y');
                }
                return '-';
            })
            ->editColumn('transaction_id', function ($row) {
                if(!is_null($row->transaction_id)) {
                    return $row->transaction_id;
                }
                return '-';
            })
            ->editColumn('method', function ($row) {
                if($row->method == 'Offline' && $row->offline_payment_method) {
                    return $row->method.' ('.$row->offline_payment_method->name.')';
                }
                return $row->method;
            })
            ->addColumn('action', function ($row) {
                if($row->method == 'Stripe' && $row->invoice_id){
                    return '<a href="'.route('super-admin.stripe.invoice-download', $row->invoice_id).'" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a>';
                }

                if($row->method == 'Paypal'){
                    return '<a href="'.route('super-admin.paypal.invoice-download', $row->id).'" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a>';
                }

                if($row->method == 'Razorpay'){
                    return '<a href="'.route('super-admin.razorpay.invoice-download', $row->id).'" class="btn btn-info btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a>';
                }

                if($row->method == 'Offline') {
                    return '<a href="'.route('super-admin.offline.invoice-download', $row->id).'" class="btn btn-primary btn-circle waves-effect" data-toggle="tooltip" data-original-title="Download"><span></span> <i class="fa fa-download"></i></a>';
                }

                return '';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function paypalInvoiceDownload($id)
    {
        $this->invoice = PaypalInvoice::with(['company','currency','package'])->findOrFail($id);
        $this->superadmin = GlobalSetting::with('currency')->first();
        $this->global = $this->company = Company::with('currency')->withoutGlobalScope('active')->where('id', $this->invoice->company->id)->first();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('paypal-invoice.invoice-1', $this->data);
        $filename = $this->invoice->paid_on->format("dS M Y").'-'.$this->invoice->next_pay_date->format("dS M Y");
        return $pdf->download($filename . '.pdf');
    }

    public function download(Request $request, $invoiceId) {
        $invoice = StripeInvoice::where('invoice_id', $invoiceId)->first();
        $this->global = $this->company = Company::with('currency')->withoutGlobalScope('active')->where('id', $invoice->company_id)->first();
        $this->setStripConfigs();
        return $this->company->downloadInvoice($invoiceId, [
            'vendor'  => $this->company->company_name,
            'product' => $this->company->package->name,
            'global' => GlobalSetting::first(),
            'logo' => $this->company->logo,
        ]);
    }

    public function razorpayInvoiceDownload($id)
    {
        $this->invoice = RazorpayInvoice::with(['company','currency','package'])->findOrFail($id);
        $this->company = $this->invoice->company;
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('razorpay-invoice.invoice-1', $this->data);
        $filename = $this->invoice->pay_date->format("dS M Y").'-'.$this->invoice->next_pay_date->format("dS M Y");
        return $pdf->download($filename . '.pdf');
    }

    public function offlineInvoiceDownload($id)
    {
        $this->invoice = OfflineInvoice::with(['company','package'])->findOrFail($id);
        $pdf = app('dompdf.wrapper');
        $this->company = $this->invoice->company;
        $pdf->loadView('offline-invoice.invoice-1', $this->data);
        $filename = $this->invoice->pay_date->format('Y-m-d').'-'.$this->invoice->next_pay_date->format('Y-m-d');
        return $pdf->download($filename . '.pdf');
    }

}
