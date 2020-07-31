<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Company;
use App\Faq;
use App\FaqCategory;
use App\Helper\Reply;

use App\Http\Requests\SuperAdmin\Faq\StoreRequest;
use App\Http\Requests\SuperAdmin\Faq\UpdateRequest;
use Yajra\DataTables\Facades\DataTables;


class SuperAdminFaqController extends SuperAdminBaseController
{
    /**
     * AdminProductController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.faq';
        $this->pageIcon = 'icon-basket';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($faqCategoryId)
    {
        $this->faqCategoryId = $faqCategoryId;
        $this->faq = new Faq();

        return view('super-admin.faq-category.add-edit-faq', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, $faqCategoryId)
    {
        $faq = new Faq();
        $faq->title = $request->title;
        $faq->description = $request->description;
        $faq->faq_category_id = $request->faq_category_id;
        $faq->save();

        return Reply::success( 'messages.createSuccess');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($faqCategoryId, $id)
    {
        $this->faqCategoryId = $faqCategoryId;
        $this->faq = Faq::find($id);

        return view('super-admin.faq-category.add-edit-faq', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $faqCategoryId, $id)
    {
        $faq = Faq::find($id);
        $faq->title = $request->title;
        $faq->description = $request->description;
        $faq->faq_category_id = $request->faq_category_id;
        $faq->save();

        return Reply::success('messages.updateSuccess');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($faqCategoryId, $id)
    {
        Faq::destroy($id);

        return Reply::success('messages.deleteSuccess');
    }
}
