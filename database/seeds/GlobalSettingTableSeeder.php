<?php

use App\GlobalSetting;
use App\Package;
use Illuminate\Database\Seeder;

class GlobalSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currency =  \App\GlobalCurrency::first();

        $setting = new \App\GlobalSetting();

        $setting->currency_id = $currency->id;
        $setting->company_name = 'Froiden';
        $setting->company_email = 'company@email.com';
        $setting->company_phone = '1234567891';
        $setting->address = 'Company address';
        $setting->website = 'www.domain.com';
        $setting->save();

        $package = Package::where('default', 'trial')->first();
        if ($package) {
            $global = GlobalSetting::with('currency')->first();

            if ($global) {
                $package->currency_id = $global->currency_id;
            }
            $package->save();
        }
    }
}
