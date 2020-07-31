<?php

namespace App\Observers;


use App\CreditNotes;
use App\UniversalSearch;

class CreditNoteObserver
{

    public function saving(CreditNotes $creditNote)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $creditNote->company_id = company()->id;
        }
    }

    public function deleting(CreditNotes $creditNote){
        $universalSearches = UniversalSearch::where('searchable_id', $creditNote->id)->where('module_type', 'creditNote')->get();
        if ($universalSearches){
            foreach ($universalSearches as $universalSearch){
                UniversalSearch::destroy($universalSearch->id);
            }
        }
    }

}
