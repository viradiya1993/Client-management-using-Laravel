<div id="event-detail">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">@lang('modules.notices.notice'): {{ $notice->heading }}</h4>
    </div>
    <div class="modal-body">
        <div class="form-body">
            <div class="row">
                <div class="col-md-12 ">
                    <p>{{ $notice->description }}</p>
                </div>

            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">@lang('app.close')</button>
        </button>
    </div>
</div>