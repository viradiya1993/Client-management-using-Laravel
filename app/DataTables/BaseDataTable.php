<?php

namespace App\DataTables;

use App\Company;
use Yajra\DataTables\Services\DataTable;

class BaseDataTable extends DataTable
{
    protected $global;

    public function __construct()
    {
        $this->user = user();
        $this->global = $this->company = Company::withoutGlobalScope('active')->where('id', $this->user->company_id)->first();
    }
}
