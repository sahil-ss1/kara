<div>
    <ul class="nav-items fs-sm">
        @foreach($notifications as $notification)
            <?php $user = App\Models\User::find( $notification['data']['from'] ) ?>
            <li>
                <a class="d-flex py-2 notification_item" href="javascript:void(0)" data-id="{{ $notification->id }}">
                    <div class="flex-shrink-0 me-3 ms-2 overlay-container overlay-bottom">
                        <img class="img-avatar img-avatar48" src="{{ $user->getAvatar() }}" alt="">
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $user->name }}</div>
                        <div class="fw-normal text-muted">{{ $notification['data']['title'] }}</div>
                        <div class="fw-normal text-muted">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
    {!! $notifications->links() !!}
</div>
