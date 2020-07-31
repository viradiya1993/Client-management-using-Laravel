<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends BaseModel
{
    protected $table = 'packages';

    protected $appends = [
        'formatted_annual_price',
        'formatted_monthly_price'
    ];

    public function formatSizeUnits($bytes)
    {
       if ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' GB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes. ' MB';
        }
        else
        {
            $bytes = '0 MB';
        }

        return $bytes;
    }

    public function currency () {
        return $this->belongsTo(GlobalCurrency::class, 'currency_id')->withTrashed();
    }

    function getFormattedAnnualPriceAttribute() {
        $global = GlobalSetting::first();
        if($global->currency->currency_code == 'EUR') {
            return $this->annual_price . $global->currency->currency_symbol;
        }
        return $global->currency->currency_symbol. $this->annual_price;
    }

    function getFormattedMonthlyPriceAttribute() {
        $global = GlobalSetting::first();
        if($global->currency->currency_code == 'EUR') {
            return $this->monthly_price . $global->currency->currency_symbol;
        }
        return $global->currency->currency_symbol. $this->monthly_price;
    }
}
