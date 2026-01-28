@extends('layouts.app')

@section('content')
<form action="{{ route('user-profile-information.update', $user) }}" method="POST" enctype="multipart/form-data" id="user_update">
    @csrf
    @method('PUT')
    <x-block title="My Profile" >
        <div class="row">
            <div class="col-1">
                <div class="mb-4">
                    <div class="mb-4">
                        <img class="img-avatar" src="{{ Auth::user()->getAvatar() }}" alt="">
                    </div>
                </div>
            </div>
            <div class="col-5">
                <div class="mb-4">
                    <label for="one-profile-edit-avatar" class="form-label">{{ __('Choose a new avatar') }}</label>
                    <input class="form-control" type="file" id="one-profile-edit-avatar">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="mb-4">
                    <label class="form-label" for="name">{{ __('Name') }}</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name.." value="{{ $user->name }}" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="mb-4">
                    <label class="form-label" for="email">{{ __('Email') }}</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email.." value="{{ $user->email }}" readonly>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="mb-4">
                    <label class="form-label" for="timezone">{{ __('Timezone') }}</label>
                    <div class="default-select">
                        <select class="form-select" name="timezone" id="timezone-select">
                            @foreach($tzlist as $timezone)
                                <option value="{{ $timezone }}" {{ ($user->timezone==$timezone)?'selected':'' }}>{{ $timezone }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="mb-4">
                    <a type="button" class="el-button outlined-button orange" style=" padding: 8px 16px" href="{{ route('google.login') }}">
                        <img src="{{asset('images/settings-icons\google.svg')}}" style="display: inline-block" alt="">
                        {{ Auth::user()->google_token ? __('Re-sync with Google') : __('Sync with Google') }}
                    </a>
                    @if(Auth::user()->google_name)
                    <p>Synced with Google account - {{ Auth::user()->google_name }}</p>
                    @else
                        <p class="text-muted">Not connected. Click "Sync with Google" to connect your account.</p>
                    @endif
                </div>
            </div>
        </div>

        @if(!Auth::user()->isAdmin())
        <div class="row">
            <div class="col-6">
                <div class="mb-4">
                    @php
                        $hasHubSpot = Auth::user()->hubspot_refreshToken;
                        $organization = Auth::user()->organization();
                    @endphp
                    @if(!$hasHubSpot)
                        <a type="button" class="el-button outlined-button orange" style=" padding: 8px 16px" href="{{ route('hubspot.install') }}">
                            <i class="fa-brands fa-hubspot" style="display: inline-block; margin-right: 8px;"></i>
                            {{ __('Connect HubSpot') }}
                        </a>
                        <p class="text-muted">Not connected. Click "Connect HubSpot" to sync your CRM data.</p>
                        <p class="text-muted fs-sm mt-2">
                            <i class="fa fa-info-circle"></i> Need help? 
                            <a href="{{ route('docs.hubspot-setup-guide') }}" target="_blank">View setup guide</a>
                        </p>
                    @else
                        <a type="button" class="el-button outlined-button orange" style=" padding: 8px 16px" href="{{ route('hubspot.install') }}">
                            <i class="fa-brands fa-hubspot" style="display: inline-block; margin-right: 8px;"></i>
                            {{ __('Reconnect HubSpot') }}
                        </a>
                        @if($organization)
                            <p>Connected to HubSpot - {{ $organization->name }}</p>
                            @if($organization->hubspot_portalId)
                                <p class="text-muted fs-sm">Portal ID: {{ $organization->hubspot_portalId }}</p>
                            @endif
                        @else
                            <p class="text-success">HubSpot account connected</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-6">
                <div class="mb-4">
                    <label class="form-label" for="google_calendar_id">{{ __('Google Calendar') }}</label>
                    <div class="default-select">
                        <select class="form-select" name="google_calendar_id" id="google-calendar-id-select">
                            @foreach($calendars as $key=>$value)
                                <option value="{{ $key }}" {{ $user->google_calendar_id==$key?'selected':'' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-xl-5">
                <div class="mb-4">
                    <button type="submit" class="el-button el-button--info">
                        {{ __('Save') }}
                    </button>
                </div>
            </div>
        </div>
    </x-block>
</form>

@if ( auth()->user()->isAdmin() )
<form action="{{ route('user-password.update', $user) }}" method="POST" enctype="multipart/form-data" id="user_password_change">
    @csrf
    @method('PUT')
    <x-block title="Change Password" >
        <div class="row push">
            <div class="col-lg-4">
                <p class="fs-sm text-muted">
                    {{ __('Changing your sign in password is an easy way to keep your account secure') }}.
                </p>
            </div>
            <div class="col-lg-8 col-xl-5">
                <div class="mb-4">
                    <label class="form-label" for="current-password">{{ __('Current Password') }}</label>
                    <input type="password" class="form-control" id="current-password" name="current_password" required>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label" for="password">{{ __('Password') }}</label>
                        <div class="input-group" x-data="{ show_pass: false }">
                            <input x-bind:type=" show_pass?'text':'password' " id="password" name="password" class="form-control" required autocomplete="new-password">
                            <button type="button" class="btn btn-light" @click="show_pass = !show_pass">
                                <i class="fa-solid" x-bind:class="show_pass?'fa-eye':'fa-eye-slash'"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label" for="confirm-password">{{ __('Confirm Password') }}</label>
                        <div class="input-group" x-data="{ show_pass: false }">
                            <input x-bind:type=" show_pass?'text':'password' " id="confirm-password" name="confirm-password" class="form-control" required autocomplete="new-password">
                            <button type="button" class="btn btn-light" @click="show_pass = !show_pass">
                                <i class="fa-solid" x-bind:class="show_pass?'fa-eye':'fa-eye-slash'"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <button type="submit" class="btn btn-alt-primary" id="change_password_submit">
                        {{ __('Update') }}
                    </button>
                </div>
            </div>
        </div>
    </x-block>
</form>
@endif

@endsection

@section('scripts')
    <script>
        setPageTitle('{{ __('Profile') }}');
        addBreadcrumbItem('{{ __('Profile') }}', null);

        $('#user_update').submit(function(e){
            e.preventDefault();

            let form = new FormData($('#user_update')[0]);
            if (document.getElementById('user_update').checkValidity() === true)
                submitAjaxForm(
                    $('#user_update').attr('action'),
                    form,
                    function (e) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('Success') }}.',
                            text: '{{ __('Everything was updated perfectly') }}!',
                            customClass: {
                                confirmButton: 'el-button el-button--info'
                            },
                            buttonsStyling: false
                        })
                    }
                );
        });

        $('#change_password_submit').on('click', function(e) {
            let password = document.getElementById("password");
            let confirm_password = document.getElementById("confirm-password");
            if (password.value !== confirm_password.value) {
                confirm_password.setCustomValidity("Passwords Don't Match");
            } else {
                confirm_password.setCustomValidity('');
            }
        })
        /*
        $('#change_password_submit').on('click', function(e){
            let password = document.getElementById("password");
            let current_password = document.getElementById("current-password");
            if ( password.value !== '' ) {
                current_password.required = true;
                let confirm_password = document.getElementById("confirm-password");
                if (password.value !== confirm_password.value) {
                    confirm_password.setCustomValidity("Passwords Don't Match");
                } else {
                    confirm_password.setCustomValidity('');
                }
            }
            else current_password.required = false;
        })*/

        $('#user_password_change').submit(function(e){
            e.preventDefault();

            let form = new FormData($('#user_password_change')[0]);
            if (document.getElementById('user_password_change').checkValidity() === true)
                submitAjaxForm(
                    $('#user_password_change').attr('action'),
                    form,
                    function (e) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('Success') }}.',
                            text: '{{ __('Everything was updated perfectly') }}!',
                        })
                    }
                );
        });

        timezone_select = $("#timezone-select").select2({
            placeholder: 'Select timezone',
            dropdownAutoWidth: true,
            width: '100%',
            multiple: false,
            dropdownCssClass: 'select-default-dropdown select-dropdown-240'
        });

        timezone_select.one('select2:open', function(e) {
            $('input.select2-search__field').prop('placeholder', 'Search timezone');
        });

        google_calendar_id_select = $("#google-calendar-id-select").select2({
            placeholder: 'Select calendar',
            dropdownAutoWidth: true,
            width: '100%',
            multiple: false,
            dropdownCssClass: 'select-default-dropdown select-dropdown-240'
        });

        google_calendar_id_select.one('select2:open', function(e) {
            $('input.select2-search__field').prop('placeholder', 'Search calendar');
        });

        $('.default-select>.select2-container').addClass('select-default');

    </script>
@endsection
