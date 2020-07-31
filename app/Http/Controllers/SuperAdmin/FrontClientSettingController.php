<?php

namespace App\Http\Controllers\SuperAdmin;

use App\FrontClients;
use App\FrontDetail;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\Feature\UpdateTitleRequest;
use App\Http\Requests\SuperAdmin\FrontClientSetting\StoreRequest;
use App\Http\Requests\SuperAdmin\FrontClientSetting\UpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FrontClientSettingController extends SuperAdminBaseController
{
    /**
     * SuperAdminInvoiceController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Front Client Settings';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->frontClients = FrontClients::all();
        $this->frontDetail  = FrontDetail::first();

        return view('super-admin.front-client-settings.index', $this->data);
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->frontClients = FrontClients::all();

        return view('super-admin.front-client-settings.create', $this->data);
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {
        $frontClients = new FrontClients();
        $frontClients->title = $request->title;
        if ($request->hasFile('image')) {
            $frontClients->image = Files::upload($request->image, 'front/client');
        }

        $frontClients->save();

        return Reply::redirect(route('super-admin.client-settings.index'), 'messages.testimonial.addedSuccess');

    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->frontClient = FrontClients::findOrFail($id);

        return view('super-admin.front-client-settings.edit', $this->data);
    }

    /**
     * @param UpdateRequest $request
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function update(UpdateRequest $request, $id)
    {
        $frontClients = FrontClients::findOrFail($id);


        $frontClients->title = $request->title;

        if ($request->hasFile('image')) {
            Files::deleteFile($frontClients->image, 'front/feature');
            $frontClients->image = Files::upload($request->image, 'front/feature');
        }

        $frontClients->save();

        return Reply::redirect(route('super-admin.client-settings.index'), 'messages.frontClient.addedSuccess');

    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function destroy(Request $request, $id)
    { FrontClients::destroy($id);
        return Reply::redirect(route('super-admin.client-settings.index'), 'messages.frontClient.deletedSuccess');
    }

    /**
     * @param UpdateTitleRequest $request
     * @return array
     */
    public function updateTitles(UpdateTitleRequest $request)
    {
        $frontClients = FrontDetail::first();

        $frontClients->client_title = $request->title;
        $frontClients->client_details = $request->detail;
        $frontClients->save();

        return Reply::success('messages.updatedSuccess');

    }

}
