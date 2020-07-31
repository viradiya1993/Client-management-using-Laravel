<li class="top-notifications">
    <div class="message-center">
        <a href="javascript:;" class="show-all-notifications">
            <div class="user-img">
                <span class="btn btn-circle btn-success"><i class="ti-layout-list-thumb"></i></span>
            </div>
            <div class="mail-contnet">
                <span class="mail-desc m-0">{{ ucfirst($notification->data['heading']) }} - @lang('email.taskComplete.subject')!</span>
                <span class="time">@if($notification->completed_on) {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->completed_on)->diffForHumans() }} @endif</span>
            </div>
        </a>
    </div>
</li>
