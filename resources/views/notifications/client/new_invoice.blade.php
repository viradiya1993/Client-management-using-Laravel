<li class="top-notifications">
    <div class="message-center">
        <a href="javascript:;" class="show-all-notifications">
            <div class="user-img">
                <span class="btn btn-circle btn-inverse"><i class="icon-doc"></i></span>
            </div>
            <div class="mail-contnet">
                <span class="mail-desc m-0">@lang('app.new') @lang('app.invoice') -
                    @if(isset($notification->data['project']['project_name']))
                        @lang('app.project') {{ ucwords($notification->data['project']['project_name']) }}
                    @elseif(isset($notification->data['project_name']))
                        @lang('app.project') {{ ucwords($notification->data['project_name']) }}
                    @elseif(isset($notification->data['invoice_number']))
                        {{ $notification->data['invoice_number'] }}
                    @else
                        @lang('messages.newInvoiceCreated')
                    @endif
                </span> <span class="time">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</span>
            </div>
        </a>
    </div>
</li>