@extends('layouts.login')


@section('content')
    <div class="col-md-8 col-lg-6 col-xl-4">
        <!-- Sign In Block -->
        <div class="block block-rounded mb-0">
            <div class="block-header block-header-default">
                <h3 class="block-title">{{ __('Create Account') }}</h3>
                <div class="block-options">
                    <a class="btn-block-option fs-sm" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#one-signup-terms">{{ __('View Terms') }}</a>
                    <a class="btn-block-option" href="{{ route('login') }}" data-bs-toggle="tooltip" data-bs-placement="left" title="{{ __('Sign In') }}">
                        <i class="fa fa-sign-in-alt"></i>
                    </a>
                </div>
            </div>
            <div class="block-content">
                <div class="p-sm-3 px-lg-4 px-xxl-5 py-lg-5">
                    <h1 class="h2 mb-1">{{ config('app.name', 'Laravel') }}</h1>
                    <p class="fw-medium text-muted">
                        {{ __('Please fill the following details to create a new account') }}.
                    </p>
                    <!-- Sign In Form -->
                    <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _js/pages/op_auth_signin.js) -->
                    <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="py-3">
                            <div class="mb-4">
                                <input type="text" id="name"  name="name" class="form-control form-control-lg form-control-alt" value="{{ old('name') }}"
                                       placeholder="{{ __('Name') }}" required autocomplete="name" autofocus >
                            </div>
                            <div class="mb-4">
                                <input type="email" id="email" name="email" class="form-control form-control-alt form-control-lg" value="{{ old('email') }}"
                                       placeholder="{{ __('E-Mail Address') }}" required autocomplete="email" autofocus>
                            </div>
                            <div class="mb-4">
                                <div class="input-group" x-data="{ show_pass: false }">
                                    <input x-bind:type=" show_pass?'text':'password' " id="password" name="password" class="form-control form-control-alt form-control-lg"
                                           placeholder="{{ __('Password') }}" required autocomplete="new-password">
                                    <button type="button" class="btn btn-light" @click="show_pass = !show_pass">
                                        <i class="fa-solid" x-bind:class="show_pass?'fa-eye':'fa-eye-slash'"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="input-group" x-data="{ show_pass: false }">
                                    <input x-bind:type=" show_pass?'text':'password' " id="password_confirmation" name="password_confirmation" class="form-control form-control-alt form-control-lg"
                                           placeholder="{{ __('Confirm Password') }}" required autocomplete="new-password">
                                    <button type="button" class="btn btn-light" @click="show_pass = !show_pass">
                                        <i class="fa-solid" x-bind:class="show_pass?'fa-eye':'fa-eye-slash'"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="form-check">
                                    <input type="checkbox" id="signup-terms" name="signup-terms" class="form-check-input" required>
                                    <label class="form-check-label" for="signup-terms">{!! __('I agree to Terms &amp; Conditions') !!}</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6 col-xl-5">
                                <button type="submit" class="btn w-100 btn-alt-success">
                                    <i class="fa fa-fw fa-plus me-1 opacity-50"></i> {{ __('Register') }}
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

    <!-- Terms Modal -->
    <div class="modal fade" id="one-signup-terms" tabindex="-1" role="dialog" aria-labelledby="one-signup-terms" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-popout" role="document">
            <div class="modal-content">
                <div class="block block-rounded block-transparent mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Terms &amp; Conditions</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <p>Dolor posuere proin blandit accumsan senectus netus nullam curae, ornare laoreet adipiscing luctus mauris adipiscing pretium eget fermentum, tristique lobortis est ut metus lobortis tortor tincidunt himenaeos habitant quis dictumst proin odio sagittis purus mi, nec taciti vestibulum quis in sit varius lorem sit metus mi.</p>
                        <p>Dolor posuere proin blandit accumsan senectus netus nullam curae, ornare laoreet adipiscing luctus mauris adipiscing pretium eget fermentum, tristique lobortis est ut metus lobortis tortor tincidunt himenaeos habitant quis dictumst proin odio sagittis purus mi, nec taciti vestibulum quis in sit varius lorem sit metus mi.</p>
                        <p>Dolor posuere proin blandit accumsan senectus netus nullam curae, ornare laoreet adipiscing luctus mauris adipiscing pretium eget fermentum, tristique lobortis est ut metus lobortis tortor tincidunt himenaeos habitant quis dictumst proin odio sagittis purus mi, nec taciti vestibulum quis in sit varius lorem sit metus mi.</p>
                        <p>Dolor posuere proin blandit accumsan senectus netus nullam curae, ornare laoreet adipiscing luctus mauris adipiscing pretium eget fermentum, tristique lobortis est ut metus lobortis tortor tincidunt himenaeos habitant quis dictumst proin odio sagittis purus mi, nec taciti vestibulum quis in sit varius lorem sit metus mi.</p>
                        <p>Dolor posuere proin blandit accumsan senectus netus nullam curae, ornare laoreet adipiscing luctus mauris adipiscing pretium eget fermentum, tristique lobortis est ut metus lobortis tortor tincidunt himenaeos habitant quis dictumst proin odio sagittis purus mi, nec taciti vestibulum quis in sit varius lorem sit metus mi.</p>
                    </div>
                    <div class="block-content block-content-full text-end bg-body">
                        <button type="button" class="btn btn-sm btn-alt-secondary me-1" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">I Agree</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Terms Modal -->

@endsection
