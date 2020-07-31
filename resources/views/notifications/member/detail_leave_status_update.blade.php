<div class="media">
    <div class="media-body">
        <h5 class="media-heading"><span class="btn btn-circle btn-info"><i class="icon-list"></i></span>@if($notification->data['status'] == 'approved') @lang('email.leave.approve') @else @lang('email.leave.reject') @endif</h5>
    </div>
    <h6><i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</i></h6>
</div>
