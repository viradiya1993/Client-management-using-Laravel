<?php

namespace App\Http\Controllers\SuperAdmin;

use App\FrontClients;
use App\FrontDetail;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\Feature\UpdateTitleRequest;
use App\Http\Requests\SuperAdmin\FrontClientSetting\StoreRequest;
use App\Http\Requests\SuperAdmin\FrontClientSetting\UpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CtaSettingController extends SuperAdminBaseController
{
    /**
     * SuperAdminInvoiceController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Front CTA Settings';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->frontDetail = FrontDetail::first();
        return view('super-admin.cta-settings.index', $this->data);

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

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {


    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //
    }


    public function update(UpdateTitleRequest $request, $id)
    {
         $frontClients = FrontDetail::first();

        $frontClients->cta_title = $request->title;
        $frontClients->cta_detail = $request->detail;
        $frontClients->save();

        return Reply::success('messages.updatedSuccessfully');

    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function destroy(Request $request, $id)
    {
        //
    }
}
