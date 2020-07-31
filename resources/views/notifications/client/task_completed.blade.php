<li class="top-notifications">
    <div class="message-center">
        <a href="javascript:;" class="show-all-notifications">
            <div class="user-img">
                <span class="btn btn-circle btn-success"><i class="ti-layout-list-thumb"></i></span>
            </div>
            <div class="mail-contnet">
                <span class="mail-desc m-0">{{ ucfirst($notification->data['heading']) }} - @lang('email.taskComplete.subject')!</span> <span class="time">{{ \Carbon\Carbon::parse($notification->data['completed_on'])->diffForHumans() }}</span>
            </div>
        </a>
    </div>
</li>