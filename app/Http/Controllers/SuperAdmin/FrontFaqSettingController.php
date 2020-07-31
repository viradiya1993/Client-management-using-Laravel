<?php

namespace App\Http\Controllers\SuperAdmin;

use App\FrontClients;
use App\FrontDetail;
use App\FrontFaq;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\Feature\UpdateTitleRequest;
use App\Http\Requests\SuperAdmin\FrontFaqSetting\StoreRequest;
use App\Http\Requests\SuperAdmin\FrontFaqSetting\UpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FrontFaqSettingController extends SuperAdminBaseController
{
    /**
     * SuperAdminInvoiceController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Front Faq Settings';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->frontFaqs    = FrontFaq::all();
        $this->frontDetail  = FrontDetail::first();

        return view('super-admin.front-faq-settings.index', $this->data);
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->frontClients = FrontClients::all();

        return view('super-admin.front-faq-settings.create', $this->data);
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {
        $frontFaq = new FrontFaq();

        $frontFaq->question = $request->question;
        $frontFaq->answer   = $request->answer;
        $frontFaq->save();

        return Reply::redirect(route('super-admin.faq-settings.index'), 'messages.frontFaq.addedSuccess');

    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->frontFaq = FrontFaq::findOrFail($id);

        return view('super-admin.front-faq-settings.edit', $this->data);
    }

    /**
     * @param UpdateRequest $request
     * @param $id
     * @return array
     */
    public function update(UpdateRequest $request, $id)
    {
        $frontFaq = FrontFaq::findOrFail($id);

        $frontFaq->question = $request->question;
        $frontFaq->answer   = $request->answer;
        $frontFaq->save();

        return Reply::redirect(route('super-admin.faq-settings.index'), 'messages.frontFaq.updatedSuccess');

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
