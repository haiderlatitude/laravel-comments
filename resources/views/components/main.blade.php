@if(isset($ui) && $ui == 'bootstrap')
    @include('comments::bootstrap.components.comments')
@else
    @include('comments::tailwind.components.comments')
@endif
