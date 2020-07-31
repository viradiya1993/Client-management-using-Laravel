<?php

namespace App;

use App\Observers\CurrencyObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Currency extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        static::observe(CurrencyObserver::class);
        static::addGlobalScope(new CompanyScope);
    }
}
