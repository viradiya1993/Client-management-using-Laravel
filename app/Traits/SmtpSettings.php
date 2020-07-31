<?php

/**
 * Created by PhpStorm.
 * User: DEXTER
 * Date: 24/05/17
 * Time: 11:29 PM
 */

namespace App\Traits;

use App\GlobalSetting;
use App\PushNotificationSetting;
use App\SmtpSetting;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\Facades\Config;

trait SmtpSettings
{

    public function setMailConfigs()
    {
        $smtpSetting = SmtpSetting::first();
        $pushSetting = PushNotificationSetting::first();
        $company = company();
        $settings = GlobalSetting::first();

        $companyName = $company ? $company->company_name : $smtpSetting->mail_from_name;

        if (\config('app.env') !== 'development') {
            Config::set('mail.driver', $smtpSetting->mail_driver);
            Config::set('mail.host', $smtpSetting->mail_host);
            Config::set('mail.port', $smtpSetting->mail_port);
            Config::set('mail.username', $smtpSetting->mail_username);
            Config::set('mail.password', $smtpSetting->mail_password);
            Config::set('mail.encryption', $smtpSetting->mail_encryption);
            Config::set('mail.from.name', $companyName);
            Config::set('mail.from.address', $smtpSetting->mail_from_email);
        }
        Config::set('services.onesignal.app_id', $pushSetting->onesignal_app_id);
        Config::set('services.onesignal.rest_api_key', $pushSetting->onesignal_rest_api_key);

        Config::set('app.name', $companyName);

        if ($company) {
            Config::set('app.logo', $company->logo_url);
        } else {
            Config::set('app.logo', $settings->logo_url);
        }

        (new MailServiceProvider(app()))->register();
    }
}
