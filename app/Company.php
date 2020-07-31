<?php

namespace App;

use App\Observers\CompanyObserver;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Invoice;
use Stripe\Invoice as StripeInvoice;

class Company extends BaseModel
{
    protected $table = 'companies';
    protected $dates = ['trial_ends_at', 'licence_expire_on', 'created_at', 'updated_at', 'last_login'];
    protected $fillable = ['last_login', 'company_name', 'company_email', 'company_phone', 'website', 'address', 'currency_id', 'timezone', 'locale', 'date_format', 'time_format', 'week_start', 'longitude', 'latitude'];
    protected $appends = ['logo_url'];
    use Notifiable, Billable;

    public function findInvoice($id)
    {
        try {
            $stripeInvoice = StripeInvoice::retrieve(
                $id,
                $this->getStripeKey()
            );

            $stripeInvoice->lines = StripeInvoice::retrieve($id, $this->getStripeKey())
                ->lines
                ->all(['limit' => 1000]);

            $stripeInvoice->date = $stripeInvoice->created;
            return new Invoice($this, $stripeInvoice);

        } catch (\Exception $e) {
            //
        }


    }

    public static function boot()
    {
        parent::boot();
        static::observe(CompanyObserver::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function employees()
    {
        return $this->hasMany(User::class)
            ->join('employee_details', 'employee_details.user_id', 'users.id');
    }


    public function getLogoUrlAttribute()
    {
        if (is_null($this->logo)) {
            $global = GlobalSetting::first();
            return $global->logo_url;
        }
        return asset_url('app-logo/' . $this->logo);
    }
}
