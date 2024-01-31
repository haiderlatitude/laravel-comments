@inject('markdown', 'Parsedown')
@php
    // TODO: There should be a better place for this.
    $markdown->setSafeMode(true);
@endphp

<div id="comment-{{ $comment->getKey() }}" class="media flex mb-5">
    <img class="mr-3 mt-1 ml-1 max-w-10 max-h-10"
         src="https://www.gravatar.com/avatar/{{ md5($comment->commenter->email ?? $comment->guest_email) }}.jpg?s=64"
         alt="{{ $comment->commenter->name ?? $comment->guest_name }} Avatar">
    <div class="media-body mb-5">
        <h5 class="mt-0 mb-1 text-sm text-gray-500">{{ $comment->commenter->name ?? $comment->guest_name }} <small
                    class="text-sm text-gray-500">- {{ $comment->created_at->diffForHumans() }}</small></h5>
        <div style="white-space: pre-wrap;">{!! $markdown->line($comment->comment) !!}</div>

        <div>
            @if(config('comments.can_reply'))
                <!-- If you don't have roles and permissions implemented in your project,
                    this 'if' check is used to enable/disable the functionality -->
                @can('reply-to-comment', $comment)
                    <button data-toggle="modal" data-target="#reply-modal-{{ $comment->getKey() }}"
                            class="btn btn-sm btn-link text-uppercase">@lang('comments::comments.reply')</button>
                @endcan
            @endif
            @if(config('comments.can_edit'))
                <!-- If you don't have roles and permissions implemented in your project,
                    this 'if' check is used to enable/disable the functionality -->
                @can('edit-comment', $comment)
                    <button data-toggle="modal" data-target="#comment-modal-{{ $comment->getKey() }}"
                            class="text-xs text-gray-500">@lang('comments::comments.edit')</button>
                @endcan
            @endif
            @if(config('comments.can_delete'))
                <!-- If you don't have roles and permissions implemented in your project,
                    this 'if' check is used to enable/disable the functionality -->
                @can('delete-comment', $comment)
                    <a href="{{ route('comments.destroy', $comment->getKey()) }}"
                       onclick="event.preventDefault();document.getElementById('comment-delete-form-{{ $comment->getKey() }}').submit();"
                       class="text-xs text-gray-500">@lang('comments::comments.delete')</a>
                    <form id="comment-delete-form-{{ $comment->getKey() }}"
                          action="{{ route('comments.destroy', $comment->getKey()) }}" method="POST"
                          style="display: none;">
                        @method('DELETE')
                        @csrf
                    </form>
                @endcan
            @endif
        </div>

        @if(config('comments.can_edit'))
            <!-- If you don't have roles and permissions implemented in your project,
                    this 'if' check is used to enable/disable the functionality -->
            @can('edit-comment', $comment)
                <div class="modal fade" id="comment-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('comments.update', $comment->getKey()) }}">
                                @method('PUT')
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">@lang('comments::comments.edit_comment')</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label
                                                for="message">@lang('comments::comments.update_your_message_here')</label>
                                        <textarea required class="form-control" name="message"
                                                  rows="3">{{ $comment->comment }}</textarea>
                                        <small
                                                class="form-text text-muted">@lang('comments::comments.markdown_cheatsheet', ['url' => 'https://help.github.com/articles/basic-writing-and-formatting-syntax'])</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase"
                                            data-dismiss="modal">@lang('comments::comments.cancel')</button>
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-success text-uppercase">@lang('comments::comments.update')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        @endif


        @if(config('comments.can_reply'))
            <!-- If you don't have roles and permissions implemented in your project,
                    this 'if' check is used to enable/disable the functionality -->
            @can('reply-to-comment', $comment)
                <div class="modal fade" id="reply-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('comments.reply', $comment->getKey()) }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">@lang('comments::comments.reply_to_comment')</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="message">@lang('comments::comments.enter_your_message_here')</label>
                                        <textarea required class="form-control" name="message" rows="3"></textarea>
                                        <small
                                                class="form-text text-muted">@lang('comments::comments.markdown_cheatsheet', ['url' => 'https://help.github.com/articles/basic-writing-and-formatting-syntax'])</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase"
                                            data-dismiss="modal">@lang('comments::comments.cancel')</button>
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-success text-uppercase">@lang('comments::comments.reply')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        @endif

        <br/>{{-- Margin bottom --}}

            <?php
            if (!isset($indentationLevel)) {
                $indentationLevel = 1;
            } else {
                $indentationLevel++;
            }
            ?>

        {{-- Recursion for children --}}
        @if($grouped_comments->has($comment->getKey()) && $indentationLevel <= $maxIndentationLevel)
            {{-- TODO: Don't repeat code. Extract to a new file and include it. --}}
            @foreach($grouped_comments[$comment->getKey()] as $child)
                @include('comments::_comment', [
                    'comment' => $child,
                    'grouped_comments' => $grouped_comments
                ])
            @endforeach
        @endif

    </div>
</div>

{{-- Recursion for children --}}
@if($grouped_comments->has($comment->getKey()) && $indentationLevel > $maxIndentationLevel)
    {{-- TODO: Don't repeat code. Extract to a new file and include it. --}}
    @foreach($grouped_comments[$comment->getKey()] as $child)
        @include('comments::_comment', [
            'comment' => $child,
            'grouped_comments' => $grouped_comments
        ])
    @endforeach
@endif