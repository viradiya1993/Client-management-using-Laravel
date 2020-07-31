<?php

namespace App\Http\Controllers;

use App\GdprSetting;
use App\GlobalSetting;
use App\Setting;
use Carbon\Carbon;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, AppBoot;

    public function __construct()
    {
        $this->showInstall();

        $this->checkMigrateStatus();
        $this->global = GlobalSetting::first();
        $this->superadmin = GlobalSetting::with('currency')->first();

        config(['app.name' => $this->global->company_name]);
        config(['app.url' => url('/')]);

        App::setLocale($this->superadmin->locale);
        Carbon::setLocale($this->superadmin->locale);
        setlocale(LC_TIME, 'en' . '_' . strtoupper('en'));

        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user && $user->super_admin == 1) {
                config(['froiden_envato.allow_users_id' => true]);
            }

            return $next($request);
        });

    }

    public function checkMigrateStatus()
    {

        $status = Artisan::call('migrate:check');

        if ($status && !request()->ajax()) {
            Artisan::call('migrate', array('--force' => true)); //migrate database
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
        }
    }
}
