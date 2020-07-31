<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\DataTables\Admin\ExpensesDataTable;
use App\Expense;
use App\Helper\Reply;
use App\Http\Requests\Expenses\StoreExpense;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ManageExpensesController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.expenses';
        $this->pageIcon = 'ti-shopping-cart';
        $this->middleware(function ($request, $next) {
            if (!in_array('expenses', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(ExpensesDataTable $dataTable)
    {
        $this->employees = User::allEmployees();
        return $dataTable->render('admin.expenses.index', $this->data);
    }

    public function create()
    {
        $this->currencies = Currency::all();
        $this->employees = User::allEmployees();

        $employees = $this->employees->toArray();
        foreach ($employees as $key => $employee) {
            $user = User::select('id', 'name')->where('id', $employee['id'])->first();
            $user_arr = [
                'id' => $user->id,
                'name' => $user->name
            ];
            $employee = array_add($employee, 'user', $user_arr);
            $employees[$key] = $employee;
        }
        foreach ($this->employees as $employee) {
            $filtered_array = array_filter($employees, function ($item) use ($employee) {
                return $item['user']['id'] == $employee->id;
            });
            $projects = [];

            foreach ($employee->member as $member) {
                if (!is_null($member->project)) {
                    array_push($projects, $member->project()->select('id', 'project_name')->first()->toArray());
                }
            }
            $employees[key($filtered_array)]['user'] = array_add(reset($filtered_array)['user'], 'projects', $projects);
        }
        $this->employees = $employees;
        return view('admin.expenses.create', $this->data);
    }

    public function store(StoreExpense $request)
    {
        $expense = new Expense();
        $expense->item_name = $request->item_name;
        $expense->purchase_date = Carbon::createFromFormat($this->global->date_format, $request->purchase_date)->format('Y-m-d');
        $expense->purchase_from = $request->purchase_from;
        $expense->price = round($request->price, 2);
        $expense->currency_id = $request->currency_id;
        $expense->user_id = $request->user_id;

        if ($request->project_id > 0) {
            $expense->project_id = $request->project_id;
        }

        if ($request->hasFile('bill')) {
            $expense->bill = $request->bill->hashName();
            $request->bill->store('expense-invoice');
            //            dd($expense->bill);
            // $img = Image::make('user-uploads/expense-invoice/' . $expense->bill);
            // $img->resize(500, null, function ($constraint) {
            //     $constraint->aspectRatio();
            // });
            // $img->save();
        }

        $expense->status = 'approved';
        $expense->save();

        return Reply::redirect(route('admin.expenses.index'), __('messages.expenseSuccess'));
    }

    public function edit($id)
    {
        $this->expense = Expense::findOrFail($id);
        $this->employees = User::allEmployees();

        $employees = $this->employees->toArray();
        foreach ($employees as $key => $employee) {
            $user = User::select('id', 'name')->where('id', $employee['id'])->first();
            $user_arr = [
                'id' => $user->id,
                'name' => $user->name
            ];
            $employee = array_add($employee, 'user', $user_arr);
            $employees[$key] = $employee;
        }
        foreach ($this->employees as $employee) {
            $filtered_array = array_filter($employees, function ($item) use ($employee) {
                return $item['user']['id'] == $employee->id;
            });
            $projects = [];

            foreach ($employee->member as $member) {
                if (!is_null($member->project)) {
                    array_push($projects, $member->project()->select('id', 'project_name')->first()->toArray());
                }
            }
            $employees[key($filtered_array)]['user'] = array_add(reset($filtered_array)['user'], 'projects', $projects);
        }

        $this->employees = $employees;
        $this->currencies = Currency::all();

        return view('admin.expenses.edit', $this->data);
    }

    public function update(StoreExpense $request, $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->item_name = $request->item_name;
        $expense->purchase_date = Carbon::createFromFormat($this->global->date_format, $request->purchase_date)->format('Y-m-d');
        $expense->purchase_from = $request->purchase_from;
        $expense->price = round($request->price, 2);
        $expense->currency_id = $request->currency_id;
        $expense->user_id = $request->user_id;

        if ($request->project_id > 0) {
            $expense->project_id = $request->project_id;
        } else {
            $expense->project_id = null;
        }

        if ($request->hasFile('bill')) {
            File::delete(public_path() . '/user-uploads/expense-invoice/' . $expense->bill);

            $expense->bill = $request->bill->hashName();
            $request->bill->store('expense-invoice');
            // $img = Image::make('user-uploads/expense-invoice/' . $expense->bill);
            // $img->resize(500, null, function ($constraint) {
            //     $constraint->aspectRatio();
            // });
            // $img->save();
        }

        $previousStatus = $expense->status;

        $expense->status = $request->status;
        $expense->save();

        return Reply::redirect(route('admin.expenses.index'), __('messages.expenseUpdateSuccess'));
    }

    public function destroy($id)
    {
        Expense::destroy($id);

        return Reply::success(__('messages.expenseDeleted'));
    }


    public function export($startDate, $endDate, $status, $employee)
    {

        $payments = Expense::select('expenses.id', 'expenses.item_name', 'expenses.price', 'users.name', 'expenses.purchase_date', 'expenses.currency_id', 'currencies.currency_symbol', 'expenses.purchase_from', 'expenses.status', 'expenses.bill')
            ->join('users', 'users.id', 'expenses.user_id')
            ->join('currencies', 'currencies.id', 'expenses.currency_id');

        if ($startDate !== null && $startDate != 'null' && $startDate != '') {
            $payments = $payments->where(DB::raw('DATE(expenses.`purchase_date`)'), '>=', $startDate);
        }

        if ($endDate !== null && $endDate != 'null' && $endDate != '') {
            $payments = $payments->where(DB::raw('DATE(expenses.`purchase_date`)'), '<=', $endDate);
        }

        if ($status != 'all' && !is_null($status)) {
            $payments = $payments->where('expenses.status', '=', $status);
        }

        if ($employee != 'all' && !is_null($employee)) {
            $payments = $payments->where('expenses.user_id', '=', $employee);
        }


        $attributes =  ['price', 'currency_symbol', 'purchase_date', 'user_id', 'currency_id', 'currency'];

        $payments = $payments->get()->makeHidden($attributes);

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Item Name', 'Employee', 'Purchased From', 'Status', 'View Invoice', 'Price', 'Purchase Date'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($payments as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('expense', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Expense');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('expense file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);

                $sheet->row(1, function ($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold'       =>  true
                    ));
                });
                $column = 'F';
                $lastRow = $sheet->getHighestRow();
                for ($row = 1; $row <= $lastRow; $row++) {
                    $cell = $sheet->getCell($column . $row);
                    $cell->getHyperlink()
                        ->setUrl(asset_url('expense-invoice/') . '/' . $cell);
                }
            });
        })->download('xlsx');
    }
}
