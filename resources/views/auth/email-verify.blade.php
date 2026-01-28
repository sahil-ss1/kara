@extends('layouts.login')


@section('content')
    <div class="col-md-8 col-lg-6 col-xl-4">
        <!-- Sign In Block -->
        <div class="block block-rounded mb-0">
            <div class="block-header block-header-default">
                <h3 class="block-title">{{ __('Verify Your Email Address') }}</h3>
                <div class="block-options">
                    <a class="btn-block-option" href="{{ route('login') }}" data-bs-toggle="tooltip" data-bs-placement="left" title="{{ __('Sign In') }}">
                        <i class="fa fa-sign-in-alt"></i>
                    </a>
                </div>
            </div>
            <div class="block-content">
                <div class="p-sm-3 px-lg-4 px-xxl-5 py-lg-5">
                    <h1 class="h2 mb-1">{{ config('app.name', 'Laravel') }}</h1>
                    <p class="fw-medium text-muted">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </p>
                    <!-- Sign In Form -->
                    <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _js/pages/op_auth_signin.js) -->
                    <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                    <form action="{{ route('verification.resend') }}" method="POST">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-6 col-xl-5">
                                <button type="submit" class="btn w-100 btn-alt-primary">
                                    <i class="fa fa-fw fa-envelope me-1 opacity-50"></i> {{ __('click here to request another') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- END Sign In Form -->
                </div>
                @if (session('status'))
                    <div class="alert alert-info" role="alert">
                        <p class="mb-0">
                            {{ session('status') }}
                        </p>
                    </div>
                @endif
                @error('*')
                <div class="alert alert-danger" role="alert">
                    <p class="mb-0"> {{ $message }} </p>
                </div>
                @enderror
            </div>
        </div>
        <!-- END Sign In Block -->
    </div>
@endsection
