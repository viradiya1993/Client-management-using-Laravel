<?php

namespace App\Http\Controllers\SuperAdmin;

use App\GlobalCurrency;
use App\GlobalSetting;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\Settings\UpdateGlobalSettings;
use App\Package;
use App\Traits\GlobalCurrencyExchange;
use App\LanguageSetting;

class SuperAdminSettingsController extends SuperAdminBaseController
{
    use GlobalCurrencyExchange;
    /**
     * SuperAdminInvoiceController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Settings';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->global = GlobalSetting::first();
        $this->currencies = GlobalCurrency::all();
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->languageSettings = LanguageSetting::where('status', 'enabled')->get();
        $this->cachedFile = \File::exists(base_path('bootstrap/cache/config.php'));
        return view('super-admin.settings.edit', $this->data);
    }

    public function update(UpdateGlobalSettings $request, $id)
    {
        $setting = GlobalSetting::findOrFail($id);
        $oldCurrencyID = $setting->currency_id;
        $newCurrencyID = $request->input('currency_id');
        $setting->company_name = $request->input('company_name');
        $setting->company_email = $request->input('company_email');
        $setting->company_phone = $request->input('company_phone');
        $setting->website = $request->input('website');
        $setting->address = $request->input('address');

        $setting->currency_id = $request->input('currency_id');
        $setting->timezone = $request->input('timezone');
        $setting->locale = $request->input('locale');
        $setting->week_start = $request->input('week_start');
        $setting->system_update = $request->has('system_update') && $request->input('system_update') == 'on' ? 1 : 0;;
        $setting->email_verification = $request->has('email_verification') && $request->input('email_verification') == 'on' ? 1 : 0;;

        if ($oldCurrencyID != $newCurrencyID) {
            try {
                $this->updateExchangeRates();
            } catch (\Throwable $th) {
                //throw $th;
            }
            $currency = GlobalCurrency::where('id', $newCurrencyID)->first();

            $packages = Package::all();
            foreach ($packages as $package) {
                $package->annual_price = $package->annual_price * $currency->exchange_rate;
                $package->monthly_price = $package->monthly_price * $currency->exchange_rate;
                $package->currency_id = $request->input('currency_id');
                $package->save();
            }
        }

        // $setting->google_map_key = $request->input('google_map_key');
        $setting->google_recaptcha_key = $request->input('google_recaptcha_key');
        $setting->google_recaptcha_secret = $request->input('google_recaptcha_secret');

        if ($request->hasFile('logo')) {
            $setting->logo = Files::upload($request->logo, 'app-logo');
        }

        if ($request->hasFile('logo_front')) {
            $setting->logo_front = Files::upload($request->logo_front, 'app-logo');
        }

        $setting->last_updated_by = $this->user->id;

        if ($request->hasFile('login_background')) {
            $request->login_background->storeAs('user-uploads', 'login-background.jpg');
            $setting->login_background = 'login-background.jpg';
        }
        $setting->save();

        return Reply::redirect(route('super-admin.settings.index'));
    }
}
