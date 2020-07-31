<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Feature;
use App\FrontDetail;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\Feature\UpdateTitleRequest;
use App\Http\Requests\SuperAdmin\FeatureSetting\StoreRequest;
use App\Http\Requests\SuperAdmin\FeatureSetting\UpdateRequest;
use App\Http\Requests\SuperAdmin\FrontSetting\UpdateContactSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SuperAdminFeatureSettingController extends SuperAdminBaseController
{
    /**
     * SuperAdminInvoiceController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Front Feature Settings';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->type        = $request->type;
        $this->features    = Feature::where('type', $this->type)->get();
        $this->frontDetail = FrontDetail::first();

        return view('super-admin.feature-settings.index', $this->data);
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->type     = $request->type;
        $this->features = Feature::all();

        return view('super-admin.feature-settings.create', $this->data);
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {
        $feature = new Feature();
        $type                 =  $request->type;
        $feature->title       = $request->title;
        $feature->type        = $request->type;
        $feature->description = $request->description;

        if($request->has('icon')){
            $feature->icon = $request->icon;
        }
        if ($request->hasFile('image')) {
            $feature->image = Files::upload($request->image, 'front/feature');
        }

        $feature->save();

        return Reply::redirect(route('super-admin.feature-settings.index').'?type='.$type, 'messages.feature.addedSuccess');

    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->feature = Feature::findOrFail($id);
        $this->type = $request->type;

        return view('super-admin.feature-settings.edit', $this->data);
    }

    /**
     * @param UpdateRequest $request
     * @param $id
     * @return array
     */
    public function update(UpdateRequest $request, $id)
    {
        $feature = Feature::findOrFail($id);

        $feature->title = $request->title;

        $feature->type = $request->type;
        $feature->description = $request->description;
        if($request->has('icon')){
            $feature->icon = $request->icon;
        }

        if ($request->hasFile('image')) {
            Files::deleteFile($feature->image, 'front/feature');
            $feature->image = Files::upload($request->image, 'front/feature');
        }

        $type =  $request->type;
        $feature->save();

        return Reply::redirect(route('super-admin.feature-settings.index').'?type='.$type, 'messages.feature.addedSuccess');

    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function destroy(Request $request, $id)
    {
        $type =  $request->type;
        Feature::destroy($id);
        return Reply::redirect(route('super-admin.feature-settings.index').'?type='.$type, 'messages.feature.deletedSuccess');

    }

    /**
     * @param UpdateTitleRequest $request
     * @return array
     */
    public function updateTitles(UpdateTitleRequest $request)
    {
        $feature = FrontDetail::first();

        if($request->type == 'task')    {
            $feature->task_management_title  = $request->title;
            $feature->task_management_detail = $request->detail;
        }
        elseif($request->type == 'bills'){
            $feature->manage_bills_title  = $request->title;
            $feature->manage_bills_detail = $request->detail;
        }
        elseif($request->type == 'image'){
            $feature->feature_title       = $request->title;
            $feature->feature_description = $request->detail;
        }
        elseif($request->type == 'team'){
            $feature->teamates_title  = $request->title;
            $feature->teamates_detail = $request->detail;
        }
        elseif($request->type == 'apps'){
            $feature->favourite_apps_title  = $request->title;
            $feature->favourite_apps_detail = $request->detail;
        }

         $feature->save();

        return Reply::success('messages.feature.addedSuccess');

    }
}
