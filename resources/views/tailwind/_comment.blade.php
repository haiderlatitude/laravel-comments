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
        <div class="mt-0 mb-1 text-lg text-sm text-gray-500">{{ $comment->commenter->name ?? $comment->guest_name }} <small
                    class="text-sm text-gray-500">- {{ $comment->created_at->diffForHumans() }}</small></div>
        <div style="white-space: pre-wrap;">{!! $markdown->line($comment->comment) !!}</div>

        <div>
            @if(config('comments.can_reply'))
                <!-- If you don't have roles and permissions implemented in your project,
                    this 'if' check is used to enable/disable the functionality -->
                @can('reply-to-comment', $comment)
                    <button data-modal-target="reply-modal-{{ $comment->getKey() }}"
                            data-modal-toggle="reply-modal-{{ $comment->getKey() }}"
                            class="text-xs text-gray-500 hover:underline" type="button">
                        @lang('comments::comments.reply')
                    </button>
                @endcan
            @endif
            @if(config('comments.can_edit'))
                <!-- If you don't have roles and permissions implemented in your project,
                    this 'if' check is used to enable/disable the functionality -->
                @can('edit-comment', $comment)
                    <!-- Modal toggle -->
                    <button data-modal-target="edit-modal-{{ $comment->getKey() }}"
                            data-modal-toggle="edit-modal-{{ $comment->getKey() }}"
                            class="text-xs text-blue-500 hover:underline" type="button">
                        @lang('comments::comments.edit')
                    </button>
                @endcan
            @endif
            @if(config('comments.can_delete'))
                <!-- If you don't have roles and permissions implemented in your project,
                    this 'if' check is used to enable/disable the functionality -->
                @can('delete-comment', $comment)
                    <a href="{{ route('comments.destroy', $comment->getKey()) }}"
                       onclick="event.preventDefault();document.getElementById('comment-delete-form-{{ $comment->getKey() }}').submit();"
                       class="text-xs text-red-500 hover:underline">@lang('comments::comments.delete')</a>
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
                <!-- Main modal -->
                <div id="edit-modal-{{ $comment->getKey() }}" tabindex="-1" aria-hidden="true"
                     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 w-full max-w-md max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div
                                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                    @lang('comments::comments.edit_comment')
                                </div>
                                <button type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-modal-toggle="edit-modal-{{ $comment->getKey() }}">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <form method="POST" action="{{ route('comments.update', $comment->getKey()) }}" class="p-5">
                                @method('PUT')
                                @csrf
                                <div class="w-full flex flex-wrap">
                                    <div class="w-full">
                                        <label
                                                for="message"
                                                class="text-gray-500 text-xs">@lang('comments::comments.update_your_message_here')</label>
                                        <textarea required class="w-full resize-none rounded bg-gray-100" name="message" id="message"
                                                  rows="3">{{ $comment->comment }}</textarea>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit"
                                            class="text-xs text-white bg-blue-500 hover:bg-blue-600 px-3 py-2 rounded uppercase">@lang('comments::comments.update')</button>
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
                <div id="reply-modal-{{ $comment->getKey() }}" tabindex="-1" aria-hidden="true"
                     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 w-full max-w-md max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div
                                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                    @lang('comments::comments.reply_to_comment')
                                </div>
                                <button type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-modal-toggle="reply-modal-{{ $comment->getKey() }}">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <form method="POST" action="{{ route('comments.reply', $comment->getKey()) }}" class="p-5">
                                @csrf
                                <div class="w-full flex flex-wrap">
                                    <div class="w-full">
                                        <div class="mb-5 text-sm text-gray-700">
                                            <span class="text-xs text-gray-500 mr-1">Replying to:</span>
                                            <span
                                                    class="flex flex-wrap overflow-auto max-h-16 rounded bg-gray-100 border border-gray-300 mt-2 p-2">
                                                {{ $comment->comment }}
                                            </span>
                                        </div>
                                        <label
                                                for="message"
                                                class="text-gray-500 text-xs">@lang('comments::comments.enter_your_message_here')</label>
                                        <textarea required class="w-full rounded bg-gray-100 resize-none" name="message" id="message"
                                                  rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit"
                                            class="text-xs text-white bg-blue-500 hover:bg-blue-600 px-3 py-2 rounded uppercase">@lang('comments::comments.reply')</button>
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
                @include('comments::tailwind._comment', [
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
        @include('comments::tailwind._comment', [
            'comment' => $child,
            'grouped_comments' => $grouped_comments
        ])
    @endforeach
@endif
