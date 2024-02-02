@php
    if (isset($approved) and $approved == true) {
        $comments = $model->approvedComments;
    } else {
        $comments = $model->comments;
    }
@endphp

@if($comments->count() < 1)
    <div class="w-full bg-[#005FC6] text-white rounded p-5 mb-5">@lang('comments::comments.there_are_no_comments')</div>
@endif

<div>
    @php
        $comments = $comments->sortBy('created_at');

        if (isset($perPage)) {
            $page = request()->query('page', 1) - 1;

            $parentComments = $comments->where('child_id', '');

            $slicedParentComments = $parentComments->slice($page * $perPage, $perPage);

            $m = Config::get('comments.model'); // This has to be done like this, otherwise it will complain.
            $modelKeyName = (new $m)->getKeyName(); // This defaults to 'id' if not changed.

            $slicedParentCommentsIds = $slicedParentComments->pluck($modelKeyName)->toArray();

            // Remove parent Comments from comments.
            $comments = $comments->where('child_id', '!=', '');

            $grouped_comments = new \Illuminate\Pagination\LengthAwarePaginator(
                $slicedParentComments->merge($comments)->groupBy('child_id'),
                $parentComments->count(),
                $perPage
            );

            $grouped_comments->withPath(request()->url());
        } else {
            $grouped_comments = $comments->groupBy('child_id');
        }
    @endphp
    @foreach($grouped_comments as $comment_id => $comments)
        {{-- Process parent nodes --}}
        @if($comment_id == '')
            @foreach($comments as $comment)
                @include('comments::tailwind._comment', [
                    'comment' => $comment,
                    'grouped_comments' => $grouped_comments,
                    'maxIndentationLevel' => $maxIndentationLevel ?? 3
                ])
            @endforeach
        @endif
    @endforeach
</div>

@isset ($perPage)
    {{ $grouped_comments->links() }}
@endisset

@auth
    @include('comments::tailwind._form')
@elseif(Config::get('comments.guest_commenting') == true)
    @include('comments::tailwind._form', [
        'guest_commenting' => true
    ])
@else
    <div class="w-full border shadow shadow-lg rounded mb-10 p-5">
        <div class="">
            <h5 class="flex justify-start w-full font-bold w-full text-xl mb-3">@lang('comments::comments.authentication_required')</h5>
            <p class="flex justify-start w-full font-bold w-full text-lg mb-3">@lang('comments::comments.you_must_login_to_post_a_comment')</p>
            <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-700 rounded uppercase text-xs text-white px-3 py-2">@lang('comments::comments.log_in')</a>
        </div>
    </div>
@endauth
