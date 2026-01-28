<div class="member-list-container">
    <div class="member-list d-flex align-center" style="gap: 5px; width:700px; overflow-x: auto; padding-bottom: 4px;">
        @isset($members)
            @foreach($members as $member)
                <a href="{{ url('/') }}/client/profile/{{ $member->id }}" class="member-avatar-link" data-toggle="tooltip" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="{{ $member->getFullNameAttribute() }}">
                    <div class="v-avatar member-avatar" style="height: 36px; min-width: 36px; width: 50px; border-radius: 50px;
    padding: 20px; margin-bottom: 10px; font-size: 14px; font-weight:600;">
                        <span> {{ strtoupper(
                        Str::substr($member->firstName, 0, 1) .
                        Str::substr($member->lastName, 0, 1)
                    ) }}</span>
                    </div>
                </a>
            @endforeach
        @endisset
    </div>
</div>
