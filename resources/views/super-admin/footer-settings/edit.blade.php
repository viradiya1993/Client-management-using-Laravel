<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('modules.footer.editFooter')</h4>
</div>

{!!  Form::open(['url' => '' ,'method' => 'put', 'id' => 'add-edit-form','class'=>'form-horizontal']) 	 !!}
<div class="modal-body">
    <div class="box-body">
       <div class="form-group">
            <label class="col-sm-2 control-label" for="name">@lang('app.title')</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="title" name="title" value="{{ $footer->name }}">
                <div class="form-control-focus"> </div>
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">@lang('app.description')</label>
            <div class="col-sm-10">
                <textarea type="text" class="form-control summernote" id="description" rows="3" name="description" > {!! $footer->description !!} </textarea>
                <div class="form-control-focus"> </div>
                <span class="help-block"></span>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button id="save" type="button" class="btn btn-custom">@lang('app.submit')</button>
</div>
{{ Form::close() }}
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

<script>
    $('.summernote').summernote({
        height: 200,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen"]]
        ]
    });

    $('#save').click(function () {
        var url = '{{ route('super-admin.footer-settings.update', $footer->id)}}';
        $.easyAjax({
            url: url,
            container: '#add-edit-form',
            type: "POST",
            file:true,
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        })
        return false;
    })
</script>

