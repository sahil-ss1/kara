@extends('layouts.login')

@section('content')
    <div class="col-md-8 col-lg-6 col-xl-4">
        <!-- Sign In Block -->
        <div class="block block-rounded">
            <div class="block-content">
                <div class="p-2" id="login">
                    <div class="text-center" style="padding: 12px">
                        <h1 style="font-size: 38px; font-weight: 500; margin-bottom: 10px">Connection</h1>
                        <div style="color: #6c6e74">Increase your <b>sales productivity</b>.</div>
                    </div>

                    <div class="row">
                        <div class="col mb-2 mt-1">
                            <div class="row justify-content-center mt-4" id="hubspotBtn" style="display: flex">
                                <a href="{{ route('hubspot.login') }}" class="el-button hubspot-button px-2 py-3">
                                    <i class="fa-brands fa-hubspot pe-2"></i> {{ __('Sign in with HubSpot') }}
                                </a>
                            </div>
                            <div class="row justify-content-center d-flex mt-3">
                                <div onclick="openCredentialsForm()" class="el-button credentials-button px-2 py-3">
                                    {{ __('Sign in with Credentials') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="k-demo-book align-items-center justify-content-center my-4"  id="demoBookBtn">
                        {{ __('Not a Kara user?') }}
                        <a href="https://calendly.com/thomasgoubau/30-minute-meeting-thomas-goubau-clone" target="_blank" class="el-button el-button--info el-button--small ms-2">
                            {{ __('Book a demo') }}
                        </a>
                    </div>

{{--                    <h1 class="h2 mb-1">{{ config('app.name', 'Laravel') }}</h1>--}}
{{--                    <p class="fw-medium text-muted">--}}
{{--                        {{ __('Welcome, please login') }}.--}}
{{--                    </p>--}}
                    <!-- Sign In Form -->
                    <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _js/pages/op_auth_signin.js) -->
                    <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                    <div class="row mb-4" style="display: none" id="credentials-login-form">
                        <div class="col-3"></div>
                        <div class="col-6">
                            <form action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="py-3">
                                    <div class="mb-4">
                                        <input type="email" id="username" name="email" class="form-control form-control-alt form-control-lg" value="{{ old('email') }}"
                                               placeholder="{{ __('E-Mail Address') }}" required autocomplete="email" autofocus>
                                    </div>
                                    <div class="mb-4">
                                        <input type="password" id="password" name="password" class="form-control form-control-alt form-control-lg"
                                               placeholder="{{ __('Password') }}" required autocomplete="current-password">
                                    </div>
                                    <div class="mb-4">
                                        <div class="form-check">
                                            <input type="checkbox" id="remember" name="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col">
                                        <button type="submit" class="btn w-100 el-button credentials-button px-2 py-3">
                                            <i class="fa fa-fw fa-sign-in-alt me-1 opacity-50"></i>{{ __('Login') }}
                                        </button>
                                    </div>
                                </div>
                                @if (Route::has('password.request'))
                                    <a class="btn-block-option fs-sm" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                                @endif
                                @if (config('app.google_log_in'))
                                <div class="row mb-4">
                                    <div class="col">
                                        <a href="{{ route('google.login') }}" class="block-themed" style="padding:0.375rem 0.25rem">
                                            <i class="fa-brands fa-google"></i> {{ __('Login with Google') }}
                                        </a>
                                    </div>
                                </div>
                                @endif
                                @if (session('status'))
                                    <div class="card-alert card purple lighten-5">
                                        <div class="card-content purple-text">
                                            <p class="ml-4">{{ session('status') }}</p>
                                        </div>
                                    </div>
                                @endif
                                @error('*')
                                <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                                    <div class="flex-grow-1 me-3">
                                        <p class="mb-0"> {{ $message }} </p>
                                    </div>
                                </div>
                                @enderror
                            </form>
                        </div>
                        <div class="col-3"></div>
                    </div>
                    <!-- END Sign In Form -->
                </div>
            </div>
        </div>
        <!-- END Sign In Block -->
    </div>
@endsection

<script>
    function openCredentialsForm() {
        let credentials_login_form = $('#credentials-login-form');
        let hubspot_btn = $('#hubspotBtn');
        let demo_book_btn = $('#demoBookBtn');
        if(credentials_login_form.css('display') === 'none') {
            credentials_login_form.show();
            hubspot_btn.hide();
            demo_book_btn.hide();
        } else {
            credentials_login_form.hide();
            hubspot_btn.show();
            demo_book_btn.show();
        }
    }
</script>
