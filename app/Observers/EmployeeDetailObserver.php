<?php

namespace App\Observers;

use App\EmployeeDetails;
use App\UniversalSearch;

class EmployeeDetailObserver
{

    public function saving(EmployeeDetails $detail)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $detail->company_id = company()->id;
        }
    }

    public function deleting(EmployeeDetails $detail){
        $universalSearches = UniversalSearch::where('searchable_id', $detail->user_id)->where('module_type', 'employee')->get();
        if ($universalSearches){
            foreach ($universalSearches as $universalSearch){
                UniversalSearch::destroy($universalSearch->id);
            }
        }
    }

}
