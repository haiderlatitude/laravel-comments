<div class="bg-white w-full rounded-xl shadow-lg border mt-5">
    <div class="w-full mx-auto p-5">
        @if($errors->has('commentable_type'))
            <div class="bg-red-100 text-red-500 border border-red-500 rounded px-10 py-5 mb-5">
                {{ $errors->first('commentable_type') }}
            </div>
        @endif
        @if($errors->has('commentable_id'))
            <div class="bg-red-100 text-red-500 border border-red-500 rounded px-10 py-5 mb-5">
                {{ $errors->first('commentable_id') }}
            </div>
        @endif
        <form method="POST" action="{{ route('comments.store') }}">
            @csrf
            @honeypot
            <input type="hidden" name="commentable_type" value="\{{ get_class($model) }}"/>
            <input type="hidden" name="commentable_id" value="{{ $model->getKey() }}"/>

            {{-- Guest commenting --}}
            @if(isset($guest_commenting) and $guest_commenting == true)
                <div class="mb-5 w-full">
                    <label for="guest_name"
                           class="text-xs text-gray-500">@lang('comments::comments.enter_your_name_here')</label>
                    <input type="text" id="guest_name"
                           class="w-full rounded @if($errors->has('guest_name')) border border-red-500 @endif"
                           name="guest_name"/>
                    @error('guest_name')
                    <div class="text-sm text-red-500">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="my-5 w-full">
                    <label for="guest_email"
                           class="text-xs text-gray-500">@lang('comments::comments.enter_your_email_here')</label>
                    <input type="email" id="guest_email"
                           class="w-full rounded @if($errors->has('guest_email')) border border-red-500 @endif"
                           name="guest_email"/>
                    @error('guest_email')
                    <div class="text-sm text-red-500">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            @endif

            <div class="w-full">
                <label for="message"
                       class="text-sm text-gray-500">@lang('comments::comments.enter_your_message_here')</label>
                <textarea
                        class="w-full rounded-lg bg-gray-100 @if($errors->has('message')) border border-red-500 @endif"
                        name="message" rows="3"></textarea>
                @if($errors->has('message'))
                    <div class="text-red-500 text-sm">
                        @lang('comments::comments.your_message_is_required')
                    </div>
                @endif
                <button type="submit"
                        class="bg-[#005FC6] hover:bg-blue-500 text-white px-3 py-2 rounded uppercase text-xs">@lang('comments::comments.submit')</button>
            </div>
        </form>
    </div>
</div>
<br/>
