@forelse($comments as $comment)
    <div class="row b-b m-b-5 font-12">
        <div class="col-xs-12">
            <h5>{{ ucwords($comment->user->name) }}
                <span class="text-muted font-12">{{ ucfirst($comment->created_at->diffForHumans()) }}</span>
            </h5>
        </div>
        <div class="col-xs-10">
            {!! ucfirst($comment->comment)  !!}
        </div>
        <div class="col-xs-2 text-right">
            <a href="javascript:;" data-comment-id="{{ $comment->id }}"  onclick="deleteComment('{{ $comment->id }}')"  class="text-danger">@lang('app.delete')</a>
        </div>
    </div>
@empty
    <div class="col-xs-12">
        @lang('messages.noRecordFound')
    </div>
@endforelse
