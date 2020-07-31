<?php

namespace App\Http\Controllers\Front;

use App\FooterMenu;
use App\FrontDetail;
use App\FrontMenu;
use App\GlobalSetting;
use App\Http\Controllers\Controller;
use App\LanguageSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;

class FrontBaseController extends Controller
{
    /**
     * @var array
     */
    public $data = [];

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[ $name ]);
    }

    /**
     * UserBaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        try{
            rename(public_path("front-uploads"), public_path("user-uploads/front"));
        }catch (\Exception $e){}

        $this->setting = GlobalSetting::first();
        $this->languages = LanguageSetting::where('status', 'enabled')->get();
        $this->global = $this->setting;

        if(Cookie::get('language')) {
            $this->locale = Crypt::decrypt(Cookie::get('language'), false);
            App::setLocale($this->locale);
        } else {
            $this->locale = 'en';
            App::setLocale('en');
        }

        Carbon::setLocale($this->locale);

        $this->footerSettings = FooterMenu::whereNotNull('slug')->get();

        $this->frontMenu = FrontMenu::first();

        $this->frontDetail    = FrontDetail::first();

        setlocale(LC_TIME, $this->locale . '_' . strtoupper($this->locale));

        $this->detail = $this->frontDetail;
    }

}
