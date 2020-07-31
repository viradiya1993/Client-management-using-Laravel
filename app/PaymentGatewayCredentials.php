<?php

namespace App;

use App\Observers\PaymentGatewayCredentialObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewayCredentials extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        static::observe(PaymentGatewayCredentialObserver::class);

        static::addGlobalScope(new CompanyScope);
    }
}
