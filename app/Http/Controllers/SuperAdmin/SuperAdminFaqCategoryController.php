<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Company;
use App\Faq;
use App\FaqCategory;
use App\Helper\Reply;

use App\Http\Requests\SuperAdmin\FaqCategory\StoreRequest;
use App\Http\Requests\SuperAdmin\FaqCategory\UpdateRequest;
use Yajra\DataTables\Facades\DataTables;


class SuperAdminFaqCategoryController extends SuperAdminBaseController
{
    /**
     * AdminProductController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'app.menu.faqCategory';
        $this->pageIcon = 'icon-docs';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('super-admin.faq-category.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->faqCategory = new FaqCategory();

        return view('super-admin.faq-category.add-edit', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $faqCategory = new FaqCategory();
        $faqCategory->name = $request->name;
        $faqCategory->save();

        return Reply::success( 'messages.createSuccess');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->faqCategory = FaqCategory::find($id);

        return view('super-admin.faq-category.add-edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $faqCategory = FaqCategory::find($id);
        $faqCategory->name = $request->name;
        $faqCategory->save();

        return Reply::success('messages.updateSuccess');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        FaqCategory::destroy($id);

        return Reply::success('messages.deleteSuccess');
    }

    /**
     * @return mixed
     */
    public function data()
    {
        $faqCategories = FaqCategory::all();

        return Datatables::of($faqCategories)
            ->addColumn('faq', function($row) {

                $faqs = $row->faqs;

                if($faqs->count() > 0)
                {
                    $string = '<ul>';

                    foreach ($faqs as $faq)
                    {
                        $string .= '<li><a href="javascript:;" onclick="showFaqEdit('.$row->id. ','. $faq->id .')">'.$faq->title.'</a></li>';
                    }

                    $string .= '</ul>';
                } else {
                    $string = '-';
                }


                return $string;
            })
            ->addColumn('action', function($row){
                $action = '';

                $action .= '<a href="javascript:;" onclick="showFaqAdd('.$row->id.')" class="btn btn-success btn-circle"
                      data-toggle="tooltip" data-original-title="'.trans('app.addNew').' ' . trans('app.menu.faq').'"><i class="fa fa-plus" aria-hidden="true"></i></a>';

                $action .= ' <a href="javascript:;" onclick="showFaqCategoryEdit('.$row->id.')" class="btn btn-info btn-circle"
                      data-toggle="tooltip" data-original-title="'.trans('app.edit').'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                  data-toggle="tooltip" data-user-id="'.$row->id.'" data-original-title="'.trans('app.delete').'"><i class="fa fa-times" aria-hidden="true"></i></a>';

                return $action;

            })

            ->rawColumns(['action', 'faq'])
            ->make(true);
    }
}
