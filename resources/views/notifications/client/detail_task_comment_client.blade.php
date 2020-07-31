<div class="media">
    <div class="media-body">
        <h5 class="media-heading"><span class="btn btn-circle btn-success"><i class="ti-layout-list-thumb"></i></span>@lang('email.taskComment.subject') - {{ ucfirst($notification->data['heading']) }}</h5>
        </div>
    <h6><i>@if($notification->created_at){{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }} @endif</i></h6>
</div>
