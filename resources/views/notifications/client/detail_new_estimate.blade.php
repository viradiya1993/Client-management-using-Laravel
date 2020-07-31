<div class="media">
    <div class="media-body">
        <h5 class="media-heading"><span class="btn btn-circle btn-inverse"><i class="icon-doc"></i></span> @lang('messages.newEstimateReceived')</h5>
        @lang('messages.newEstimateTotal', ['total' => $notification->data['total']])
    </div>
    <h6><i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</i></h6>
</div>
