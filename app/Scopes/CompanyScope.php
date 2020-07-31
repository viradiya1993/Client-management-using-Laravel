<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class CompanyScope implements Scope
{


    public function apply(Builder $builder, Model $model)
    {
        // When user is logged in
        // auth()->user() do not work in apply so we have use auth()->hasUser()
        if (auth()->hasUser() && Schema::hasColumn($model->getTable(), 'company_id')) {
            $company = company();
            if ($company) {
                $builder->where($model->getTable() . '.company_id', '=', $company->id);
            }
        }
    }
}
