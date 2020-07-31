<?php

namespace App\Http\Controllers\SuperAdmin;

use App\FrontClients;
use App\FrontDetail;
use App\FrontFaq;
use App\FrontMenu;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\Feature\UpdateTitleRequest;
use App\Http\Requests\SuperAdmin\FrontMenuSetting\UpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FrontMenuSettingController extends SuperAdminBaseController
{
    /**
     * SuperAdminInvoiceController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Front Menu Settings';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->frontMenu    = FrontMenu::first();
        $this->frontDetail  = FrontDetail::first();

        return view('super-admin.front-menu-settings.index', $this->data);
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }


    public function store()
    {
        //

    }


    public function edit($id)
    {
        //
    }

    /**
     * @param UpdateRequest $request
     * @param $id
     * @return array
     */
    public function update(UpdateRequest $request, $id)
    {
        $frontMenu = FrontMenu::first();

        $frontMenu->home           = $request->home;
        $frontMenu->price          = $request->price;
        $frontMenu->contact        = $request->contact;
        $frontMenu->feature        = $request->feature;
        $frontMenu->get_start      = $request->get_start;
        $frontMenu->login          = $request->login;
        $frontMenu->contact_submit = $request->contact_submit;
        $frontMenu->save();

        return Reply::success('messages.updatedSuccessfully');

    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function destroy(Request $request, $id)
    {
        FrontFaq::destroy($id);
        return Reply::redirect(route('super-admin.faq-settings.index'), 'messages.frontFaq.deletedSuccess');
    }

    /**
     * @param UpdateTitleRequest $request
     * @return array
     */
    public function updateTitles(UpdateTitleRequest $request)
    {
        $frontClients = FrontDetail::first();

        $frontClients->faq_title = $request->title;
        $frontClients->faq_detail = $request->detail;
        $frontClients->save();

        return Reply::success('messages.updatedSuccessfully');

    }

}
