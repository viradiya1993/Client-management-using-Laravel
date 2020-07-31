<?php

namespace App;

use App\Observers\TaxObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Tax extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        static::observe(TaxObserver::class);

        static::addGlobalScope(new CompanyScope);
    }
}
