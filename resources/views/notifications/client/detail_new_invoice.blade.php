<div class="media">
    <div class="media-body">
        <h5 class="media-heading"><span class="btn btn-circle btn-inverse"><i class="icon-doc"></i></span> @lang('app.new') @lang('app.invoice') -
            @if(isset($notification->data['project']['project_name']))
                @lang('app.project') {{ ucwords($notification->data['project']['project_name']) }}
            @elseif(isset($notification->data['project_name']))
                @lang('app.project') {{ ucwords($notification->data['project_name']) }}
            @elseif(isset($notification->data['invoice_number']))
                {{ $notification->data['invoice_number'] }}
            @else
                @lang('messages.newInvoiceCreated')
            @endif
        </h5>
    </div>
    <h6><i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</i></h6>
</div>
