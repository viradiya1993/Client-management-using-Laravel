<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Company;
use App\Currency;
use App\EmployeeDetails;
use App\GlobalCurrency;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\Companies\DeleteRequest;
use App\Http\Requests\SuperAdmin\Companies\PackageUpdateRequest;
use App\Http\Requests\SuperAdmin\Companies\StoreRequest;
use App\Http\Requests\SuperAdmin\Companies\UpdateRequest;
use App\OfflineInvoice;
use App\OfflinePaymentMethod;
use App\Package;
use App\Role;
use App\Scopes\CompanyScope;
use App\StripeInvoice;
use App\Traits\CurrencyExchange;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SuperAdminCompanyController extends SuperAdminBaseController
{
    use CurrencyExchange;

    /**
     * AdminProductController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Companies';
        $this->pageIcon = 'icon-layers';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->totalCompanies = Company::count();
        $this->packages = Package::all();
        return view('super-admin.companies.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->currencies = GlobalCurrency::all();
        return view('super-admin.companies.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {
        DB::beginTransaction();

        $company = new Company();

        $companyDetail = $this->storeAndUpdate($company, $request);

        $globalCurrency = GlobalCurrency::findOrFail($request->currency_id);
        $currency = Currency::where('currency_code', $globalCurrency->currency_code)
            ->where('company_id', $companyDetail->id)->first();

        if (is_null($currency)) {
            $currency = new Currency();
            $currency->currency_name = $globalCurrency->currency_name;
            $currency->currency_symbol = $globalCurrency->currency_symbol;
            $currency->currency_code = $globalCurrency->currency_code;
            $currency->is_cryptocurrency = $globalCurrency->is_cryptocurrency;
            $currency->usd_price = $globalCurrency->usd_price;
            $currency->company_id = $companyDetail->id;
            $currency->save();
        }

        $company->currency_id = $currency->id;
        $company->save();

        $adminRole = Role::where('name', 'admin')->where('company_id', $companyDetail->id)->withoutGlobalScope('active')->first();

        $user = new User();
        $user->company_id = $companyDetail->id;
        $user->name = 'Admin';
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        $employee = new EmployeeDetails();
        $employee->user_id = $user->id;
        $employee->employee_id = 'emp-' . $user->id;
        $employee->company_id = $user->company_id;
        $employee->address = 'address';
        $employee->hourly_rate = '50';
        $employee->save();

        $user->roles()->attach($adminRole->id);

        $employeeRole = Role::where('name', 'employee')->where('company_id', $user->company_id)->first();
        $user->roles()->attach($employeeRole->id);

        DB::commit();
        return Reply::redirect(route('super-admin.companies.index'), 'Company added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     */
    public function show($id)
    {
        //
    }


    /**
     * @param $companyId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function editPackage($companyId)
    {
        $packages = Package::all();
        $global = $this->global;
        $company = Company::find($companyId);
        $currentPackage = Package::find($company->package_id);
        $lastInvoice = StripeInvoice::where('company_id', $companyId)->orderBy('created_at', 'desc')->first();
        $packageInfo = [];
        foreach ($packages as $package) {
            $packageInfo[$package->id] = [
                'monthly' => $package->monthly_price,
                'annual' => $package->annual_price
            ];
        }

        $offlinePaymentMethod = OfflinePaymentMethod::whereNull('company_id')->get();
        $modal = view('super-admin.companies.editPackage', compact('packages', 'company', 'currentPackage', 'lastInvoice', 'packageInfo', 'global', 'offlinePaymentMethod'))->render();

        return response(['status' => 'success', 'data' => $modal], 200);
    }

    public function updatePackage(PackageUpdateRequest $request, $companyId)
    {
        $company = Company::find($companyId);

        try {
            $package = Package::find($request->package);
            $company->package_id = $package->id;
            $company->package_type = $request->packageType;
            $company->status = 'active';

            $payDate = $request->pay_date ? Carbon::parse($request->pay_date) : Carbon::now();

            $company->licence_expire_on = ($company->package_type == 'monthly') ?
                $payDate->copy()->addMonth()->format('Y-m-d') :
                $payDate->copy()->addYear()->format('Y-m-d');

            $nextPayDate = $request->next_pay_date ? Carbon::parse($request->next_pay_date) : $company->licence_expire_on;

            if ($company->isDirty('package_id') || $company->isDirty('package_type')) {
                $offlineInvoice = new OfflineInvoice();

            } else {
                $offlineInvoice = OfflineInvoice::where('company_id', $companyId)->orderBy('created_at', 'desc')->first();
                if (!$offlineInvoice) {
                    $offlineInvoice = new OfflineInvoice();
                }
            }
            $offlineInvoice->company_id = $company->id;
            $offlineInvoice->package_id = $company->package_id;
            $offlineInvoice->package_type = $request->packageType;
            $offlineInvoice->amount = $request->amount ?: $package->{$request->packageType . '_price'};
            $offlineInvoice->pay_date = $payDate;
            $offlineInvoice->next_pay_date = $nextPayDate;
            $offlineInvoice->status = 'paid';

            $offlineInvoice->save();
            $company->save();

            return response(['status' => 'success', 'message' => 'Package Updated Successfully.'], 200);
        } catch (\Exception $e) {
            return $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->company = Company::find($id);

        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->currencies = Currency::where('company_id', $id)->get();
        $this->packages = Package::all();
        $this->companyUser = User::where('company_id',$id)->first();

        return view('super-admin.companies.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int $id
     * @return array
     */
    public function update(UpdateRequest $request, $id)
    {
        $company = Company::find($id);
        $this->storeAndUpdate($company, $request);

        $company->currency_id = $request->currency_id;
        $company->save();

        if(!is_null($request->password)){
            $user = User::where('company_id',$company->id)->first();
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();
        }


        return Reply::redirect(route('super-admin.companies.index'), 'Company updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteRequest $request
     * @param int $id
     * @return array
     */
    public function destroy(DeleteRequest $request, $id)
    {
        Company::destroy($id);
        return Reply::success('Company deleted successfully.');
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function data(Request $request)
    {
        $packages = Company::with('currency', 'package');

        if ($request->package != 'all' && $request->package != '') {
            $packages = $packages->where('package_id', $request->package);
        }

        if ($request->type != 'all' && $request->type != '') {
            $packages = $packages->where('package_type', $request->type);
        }

        return Datatables::of($packages)
            ->addColumn('action', function ($row) {
                return '<a href="' . route('super-admin.companies.edit', [$row->id]) . '" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                      <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                      data-toggle="tooltip" data-user-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
            })
            ->editColumn('company_name', function ($row) {
                return ucfirst($row->company_name).'<br />'.'<img src="' . $row->logo_url . '" class="img-responsive" style="max-height: 35px" />';
            })
            ->editColumn('status', function ($row) {
                $class = ($row->status == 'active') ? 'label-custom' : 'label-danger';
                return '<span class="label ' . $class . '">' . ucfirst($row->status) . '</span>';
            })
            ->editColumn('company_email', function ($row) {
                return '<a href="mailto:' . $row->company_email . '" target="_blank">' . $row->company_email . '</a>';
            })
            ->editColumn('last_login', function ($row) {
                if ($row->last_login != null) {
                    return $row->last_login->diffForHumans();
                }
                return '-';
            })
            ->editColumn('package', function ($row) {
                $package = '<div class="w-100 text-center">';
                $package .= '<div class="m-b-5">' . ucwords($row->package->name) . ' (' . ucfirst($row->package_type) . ')' . '</div>';

                $package .= '<a href="javascript:;" class="label label-custom package-update-button"
                      data-toggle="tooltip" data-company-id="' . $row->id . '" data-original-title="Change"><i class="fa fa-edit" aria-hidden="true"></i> Change </a>';
                $package .= '</div>';
                return $package;
            })
            ->addColumn('details', function ($row) {
                $companyUser = User::withoutGlobalScope(CompanyScope::class)->withoutGlobalScope('active')->where('company_id', $row->id)->first();

                if ($companyUser && $companyUser->email_verification_code == null) {
                    $verified = '<i class="fa fa-check-circle" style="color: green;"></i>';
                } else if ($companyUser && $companyUser->email_verification_code != null) {
                    $verified = '<i class="fa fa-times" style="color: red;"></i>';
                } else {
                    $verified = '-';
                }

                $registerDate = $row->created_at->format('d-m-Y');
                $totalUsers = User::withoutGlobalScope(CompanyScope::class)->withoutGlobalScope('active')->where('company_id', $row->id)->count();

                $string = "<ul>";
                $string .= "<li>" . __('modules.superadmin.verified') . ": " . $verified . "</li>";
                $string .= "<li>" . __('modules.superadmin.registerDate') . ": " . $registerDate . "</li>";
                $string .= "<li>" . __('modules.superadmin.totalUsers') . ": " . $totalUsers . "</li>";
                $string .= "</ul>";

                return $string;
            })
            ->rawColumns(['action', 'details', 'company_email', 'company_name', 'status', 'package'])
            ->make(true);
    }

    public function storeAndUpdate($company, $request)
    {
        $company->company_name = $request->input('company_name');
        $company->company_email = $request->input('company_email');
        $company->company_phone = $request->input('company_phone');
        $company->website = $request->input('website');
        $company->address = $request->input('address');
        $company->timezone = $request->input('timezone');
        $company->locale = $request->input('locale');
        $company->status = $request->status;

        if ($request->hasFile('logo')) {
            $company->logo = Files::upload($request->logo, 'app-logo');
        }

        $company->last_updated_by = $this->user->id;

        $company->save();


        try{
            $this->updateExchangeRatesCompanyWise($company);
        }catch(\Exception $e){

        }


        return $company;

    }
}
