<?php

namespace App\Http\Controllers\SuperAdmin;

use App\FrontDetail;
use App\GlobalCurrency;
use App\GlobalSetting;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\ContactSetting\ContactUsSettings;
use App\Http\Requests\SuperAdmin\ContactSetting\UpdateContactUsSettings;
use App\Http\Requests\SuperAdmin\FrontSetting\UpdateFrontSettings;
use App\Http\Requests\SuperAdmin\PriceSetting\UpdatePriceSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class SuperAdminFrontSettingController extends SuperAdminBaseController
{
    /**
     * SuperAdminInvoiceController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Front Settings';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->frontDetail = FrontDetail::first();
        $this->currencies  = GlobalCurrency::all();
        if($this->global->front_design == 1) {
            return view('super-admin.front-settings.new-theme.index', $this->data);
        }
        return view('super-admin.front-settings.index', $this->data);
    }

    /**
     * @param UpdateFrontSettings $request
     * @param $id
     * @return array
     */
    public function update(UpdateFrontSettings $request, $id)
    {
        $setting = FrontDetail::findOrFail($id);

        $setting->primary_color       = $request->input('primary_color');
        $setting->header_title       = $request->input('header_title');
        $setting->header_description = $request->input('header_description');
        $setting->get_started_show   = ($request->get_started_show == 'yes') ? 'yes' : 'no';
        $setting->sign_in_show       = ($request->sign_in_show == 'yes') ? 'yes' : 'no';

        if($this->global->front_design == 0){
            $setting->feature_title = $request->input('feature_title');
            $setting->feature_description = $request->input('feature_description');
            $setting->price_title = $request->input('price_title');
            $setting->price_description = $request->input('price_description');
            $setting->address = $request->input('address');
            $setting->phone = $request->input('phone');
            $setting->email = $request->input('email');
            $setting->primary_color      = $request->input('primary_color');
        }


        $links = [];
        foreach ($request->social_links as $name => $value) {
            $link_details=[];
            $link_details = Arr::add($link_details, 'name', $name);
            $link_details = Arr::add($link_details, 'link', $value);
            array_push($links, $link_details);
        }

        $setting->social_links = json_encode($links);

        if ($request->hasFile('image')) {
            Files::deleteFile($setting->image, 'front');
            $setting->image = Files::upload($request->image, 'front');
        }

        $setting->save();

        return Reply::success(__('messages.uploadSuccess'));

    }
    public function themeSetting()
    {
        $this->global      = GlobalSetting::first();
        $this->frontDetail = FrontDetail::first();
        $this->currencies  = GlobalCurrency::all();

        return view('super-admin.front-theme-settings.index', $this->data);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function themeUpdate(Request $request)
    {
        $global = GlobalSetting::first();
        $global->front_design = $request->input('theme');

        $global->save();

        return Reply::redirect(route('super-admin.theme-settings'), __('messages.updateSuccess'));

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contact()
    {
        $this->frontDetail = FrontDetail::first();
        return view('super-admin.contact-settings.index', $this->data);
    }


    public function contactUpdate(ContactUsSettings $request)
    {
        $setting = FrontDetail::first();

        $setting->address = $request->input('address');
        $setting->phone   = $request->input('phone');
        $setting->email   = $request->input('email');
        $setting->save();

        return Reply::success(__('messages.uploadSuccess'));

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function price()
    {
        $this->frontDetail = FrontDetail::first();
        return view('super-admin.price-settings.index', $this->data);
    }

    /**
     * @param UpdatePriceSettings $request
     * @return array
     */
    public function priceUpdate(UpdatePriceSettings $request)
    {
        $setting = FrontDetail::First();

        $setting->price_title       = $request->input('price_title');
        $setting->price_description = $request->input('price_description');
        $setting->save();

        return Reply::success(__('messages.uploadSuccess'));

    }
}
