<?php

namespace App\Http\Controllers\SuperAdmin;

use App\FooterMenu;
use App\FrontDetail;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\FooterSetting\FooterTextRequest;
use App\Http\Requests\SuperAdmin\FooterSetting\StoreRequest;
use App\Http\Requests\SuperAdmin\FooterSetting\UpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SuperAdminFooterSettingController extends SuperAdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Front Footer Settings';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->footer = FooterMenu::all();

        return view('super-admin.footer-settings.index', $this->data);
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->footer = FooterMenu::all();

        return view('super-admin.footer-settings.create', $this->data);
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function footerText(Request $request)
    {
        $this->frontDetail = FrontDetail::first();
        return view('super-admin.footer-settings.footer-text', $this->data);
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {
        $footer = new FooterMenu();
        $footer->name = $request->title;
        $footer->slug = Str::slug($request->title);
        $footer->description = $request->description;
        $footer->save();

        return Reply::redirect(route('super-admin.footer-settings.index'), 'messages.feature.addedSuccess');

    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->footer = FooterMenu::findOrFail($id);
        return view('super-admin.footer-settings.edit', $this->data);
    }

    /**
     * @param UpdateRequest $request
     * @param $id
     * @return array
     */
    public function update(UpdateRequest $request, $id)
    {
        $footer = FooterMenu::findOrFail($id);
        $footer->name = $request->title;
        $footer->description = $request->description;
        $footer->save();

        return Reply::redirect(route('super-admin.footer-settings.index'), 'messages.feature.addedSuccess');
    }

    /**
     * @param FooterTextRequest $request
     * @param $id
     * @return array
     */
    public function updateText(FooterTextRequest $request)
    {
        $frontClients = FrontDetail::first();
        $frontClients->footer_copyright_text = $request->footer_copyright_text;
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
        FooterMenu::destroy($id);
        return Reply::redirect(route('super-admin.footer-settings.index'), 'messages.feature.deletedSuccess');
    }
}
