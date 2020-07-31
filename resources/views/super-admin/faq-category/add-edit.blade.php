<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@if(isset($faqCategory->id)) @lang('app.edit') @else @lang('app.addNew') @endif @lang('app.faqCategory')</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'addEditFaqCategory','class'=>'ajax-form']) !!}
        @if(isset($faqCategory->id)) <input type="hidden" name="_method" value="PUT"> @endif
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>@lang('app.name')</label>
                        <input type="text" name="name" class="form-control" value="{{ $faqCategory->name ?? '' }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" id="save-faq-category" onclick="saveCategory({{ $faqCategory->id ?? '' }});return false;" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>