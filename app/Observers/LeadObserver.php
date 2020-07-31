<?php

namespace App\Observers;

use App\Lead;
use App\UniversalSearch;

class LeadObserver
{

    public function saving(Lead $lead)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $lead->company_id = company()->id;
        }
    }

    public function deleting(Lead $lead){
        $universalSearches = UniversalSearch::where('searchable_id', $lead->id)->where('module_type', 'lead')->get();
        if ($universalSearches){
            foreach ($universalSearches as $universalSearch){
                UniversalSearch::destroy($universalSearch->id);
            }
        }
    }

}
