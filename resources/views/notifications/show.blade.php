<x-block title="{{ $from->name }}" subtitle="{{ $notification->created_at->diffForHumans() }}">
    <h3> {{ $notification->data['title'] }} </h3>
    <p> {{ $notification->data['message'] }} </p>
</x-block>
