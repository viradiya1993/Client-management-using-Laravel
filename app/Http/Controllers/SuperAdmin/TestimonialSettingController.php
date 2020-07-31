<?php

namespace App\Http\Controllers\SuperAdmin;

use App\FrontDetail;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\Feature\UpdateTitleRequest;
use App\Http\Requests\SuperAdmin\TestimonialSettings\StoreRequest;
use App\Http\Requests\SuperAdmin\TestimonialSettings\UpdateRequest;
use App\Testimonials;
use Illuminate\Http\Request;

class TestimonialSettingController extends SuperAdminBaseController
{
    /**
     * SuperAdminInvoiceController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Testimonial Settings';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->testimonials = Testimonials::all();
        $this->frontDetail = FrontDetail::first();

        return view('super-admin.testimonial-settings.index', $this->data);
    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->testimonials = Testimonials::all();

        return view('super-admin.testimonial-settings.create', $this->data);
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {
        $testimonial = new Testimonials();
        $testimonial->name    = $request->name;
        $testimonial->comment = $request->comment;
        $testimonial->rating  = $request->rating;
        $testimonial->save();

        return Reply::redirect(route('super-admin.testimonial-settings.index'), 'messages.testimonial.addedSuccess');

    }

    /**
     * Display edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->testimonial = Testimonials::findOrFail($id);

        return view('super-admin.testimonial-settings.edit', $this->data);
    }

    /**
     * @param UpdateRequest $request
     * @param $id
     * @return array
     */
    public function update(UpdateRequest $request, $id)
    {
        $testimonial = Testimonials::findOrFail($id);

        $testimonial->name    = $request->name;
        $testimonial->comment = $request->comment;
        $testimonial->rating  = $request->rating;
        $testimonial->save();

        return Reply::redirect(route('super-admin.testimonial-settings.index'), 'messages.testimonial.addedSuccess');

    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function destroy(Request $request, $id)
    {
        Testimonials::destroy($id);
        return Reply::redirect(route('super-admin.testimonial-settings.index'), 'messages.testimonial.deletedSuccess');
    }

    /**
     * @param UpdateTitleRequest $request
     * @return array
     */
    public function updateTitles(UpdateTitleRequest $request)
    {
        $feature = FrontDetail::first();

        $feature->testimonial_title = $request->title;
//        $feature->testimonial_detail = $request->detail;
        $feature->save();

        return Reply::success('messages.updatedSuccess');

    }

}
