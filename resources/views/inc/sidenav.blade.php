<!--
          Sidebar Mini Mode - Display Helper classes

          Adding 'smini-hide' class to an element will make it invisible (opacity: 0) when the sidebar is in mini mode
          Adding 'smini-show' class to an element will make it visible (opacity: 1) when the sidebar is in mini mode
              If you would like to disable the transition animation, make sure to also add the 'no-transition' class to your element

          Adding 'smini-hidden' to an element will hide it when the sidebar is in mini mode
          Adding 'smini-visible' to an element will show it (display: inline-block) only when the sidebar is in mini mode
          Adding 'smini-visible-block' to an element will show it (display: block) only when the sidebar is in mini mode
-->
<nav id="sidebar" aria-label="Main Navigation">
    <!-- Side Header -->
    <div class="content-header menu-header">
        <!-- Logo -->
        <a class="fw-semibold" href="{{ url('/home') }}">
            <img src="{{asset('images/menu-icons/logo-kara.svg')}}" alt="" class="smini-visible">
            <img src="{{asset('images/menu-icons/logo-kara-full.svg')}}" alt="" class="smini-hide">
{{--            <i class="fa fa-circle-notch text-primary" style="margin-right: 0.625rem;"></i>--}}
{{--            <span class="smini-hide fs-5 tracking-wider">{{ config('app.name') }}</span>--}}
        </a>
        <!-- END Logo -->

        <!-- Options -->
        <div>
            <!-- Dark Mode -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
{{--            <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="dark_mode_toggle">--}}
{{--                <i class="far fa-moon"></i>--}}
{{--            </button>--}}
            <!-- END Dark Mode -->

            <!-- Options -->
{{--            <div class="dropdown d-inline-block ms-1">--}}
{{--                <button type="button" class="btn btn-sm btn-alt-secondary" id="sidebar-themes-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                    <i class="fa fa-brush"></i>--}}
{{--                </button>--}}
{{--                <div class="dropdown-menu dropdown-menu-end fs-sm smini-hide border-0" aria-labelledby="sidebar-themes-dropdown">--}}
{{--                    <!-- Color Themes -->--}}
{{--                    <!-- Layout API, functionality initialized in Template._uiHandleTheme() -->--}}
{{--                    <a class="dropdown-item d-flex align-items-center justify-content-between fw-medium" data-toggle="theme" data-theme="default" href="#">--}}
{{--                        <span>Default</span>--}}
{{--                        <i class="fa fa-circle text-default"></i>--}}
{{--                    </a>--}}
{{--                    <a class="dropdown-item d-flex align-items-center justify-content-between fw-medium" data-toggle="theme" data-theme="{{ url('/') }}/theme/css/themes/amethyst.min.css" href="#">--}}
{{--                        <span>Amethyst</span>--}}
{{--                        <i class="fa fa-circle text-amethyst"></i>--}}
{{--                    </a>--}}
{{--                    <a class="dropdown-item d-flex align-items-center justify-content-between fw-medium" data-toggle="theme" data-theme="{{ url('/') }}/theme/css/themes/city.min.css" href="#">--}}
{{--                        <span>City</span>--}}
{{--                        <i class="fa fa-circle text-city"></i>--}}
{{--                    </a>--}}
{{--                    <a class="dropdown-item d-flex align-items-center justify-content-between fw-medium" data-toggle="theme" data-theme="{{ url('/') }}/theme/css/themes/flat.min.css" href="#">--}}
{{--                        <span>Flat</span>--}}
{{--                        <i class="fa fa-circle text-flat"></i>--}}
{{--                    </a>--}}
{{--                    <a class="dropdown-item d-flex align-items-center justify-content-between fw-medium" data-toggle="theme" data-theme="{{ url('/') }}/theme/css/themes/modern.min.css" href="#">--}}
{{--                        <span>Modern</span>--}}
{{--                        <i class="fa fa-circle text-modern"></i>--}}
{{--                    </a>--}}
{{--                    <a class="dropdown-item d-flex align-items-center justify-content-between fw-medium" data-toggle="theme" data-theme="{{ url('/') }}/theme/css/themes/smooth.min.css" href="#">--}}
{{--                        <span>Smooth</span>--}}
{{--                        <i class="fa fa-circle text-smooth"></i>--}}
{{--                    </a>--}}
{{--                    <!-- END Color Themes -->--}}

{{--                    <div class="dropdown-divider"></div>--}}

{{--                    <!-- Sidebar Styles -->--}}
{{--                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->--}}
{{--                    <a class="dropdown-item fw-medium" data-toggle="layout" data-action="sidebar_style_light" href="javascript:void(0)">--}}
{{--                        <span>Sidebar Light</span>--}}
{{--                    </a>--}}
{{--                    <a class="dropdown-item fw-medium" data-toggle="layout" data-action="sidebar_style_dark" href="javascript:void(0)">--}}
{{--                        <span>Sidebar Dark</span>--}}
{{--                    </a>--}}
{{--                    <!-- END Sidebar Styles -->--}}

{{--                    <div class="dropdown-divider"></div>--}}

{{--                    <!-- Header Styles -->--}}
{{--                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->--}}
{{--                    <a class="dropdown-item fw-medium" data-toggle="layout" data-action="header_style_light" href="javascript:void(0)">--}}
{{--                        <span>Header Light</span>--}}
{{--                    </a>--}}
{{--                    <a class="dropdown-item fw-medium" data-toggle="layout" data-action="header_style_dark" href="javascript:void(0)">--}}
{{--                        <span>Header Dark</span>--}}
{{--                    </a>--}}
{{--                    <!-- END Header Styles -->--}}
{{--                </div>--}}
{{--            </div>--}}
            <!-- END Options -->

            <!-- Close Sidebar, Visible only on mobile screens -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <a class="d-lg-none btn btn-sm btn-alt-secondary ms-1" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
                <i class="fa fa-fw fa-times"></i>
            </a>
            <!-- END Close Sidebar -->
        </div>
        <!-- END Options -->
    </div>
    <!-- END Side Header -->

    <!-- Sidebar Scrolling -->
    <div class="js-sidebar-scroll">
      <!-- Side Navigation -->
      <div class="content-side">
        <ul class="nav-main">
            @if ( !auth()->user()->isAdmin()&&Auth::user()->member()->exists() )
                <div class="profile-pic nav-main-item" style="background: #f7f8fb url({{asset('images/menu-icons/wave-pp.svg')}}) 0 0 no-repeat; background-position: 100% 0; background-size: auto 26px;">
                    <a href="{{ url('/') }}/client/profile/{{ Auth::user()->member()->first()->id }}">
                        <div class="member-tbl-line">
                            <div class="v-avatar member-avatar" style="height: 32px; min-width: 32px; width: 32px; margin: 0 20px 0 12px; border: 1px solid #fff!important;">
                                <span>{{ Auth::user()->member()->first()->firstName[0] . Auth::user()->member()->first()->lastName[0] }}</span>
                            </div>
                            <b class="menu-title smini-hide pe-2">{{ Auth::user()->member()->first()->firstName . ' ' . Auth::user()->member()->first()->lastName }}</b>
                        </div>
                    </a>
                </div>
            @endif
            <li class="nav-main-item" id="nav-dashboard">
                <a class="menu-link" href="{{ route('home') }}">
                    <img src="{{asset('images/menu-icons/dashboard-icon.svg')}}" alt="" class="menu-icon">
                    <span class="menu-title smini-hide"> @if(auth()->user()->isAdmin()) {{ __('Dashboard') }} @else {{ __('My Teams') }} @endif </span>
                </a>
            </li>

            @if ( auth()->user()->isAdmin() )
            <li class="nav-item" id="nav-organizations">
                <a class="menu-link" href="{{ route('admin.organization.index') }}">
                    <img src="{{asset('images/menu-icons/office-building-icon.svg')}}" alt="" class="menu-icon">
                    <span class="menu-title smini-hide">{{ __('Organizations') }}</span>
                </a>
            </li>

            <li class="nav-item" id="nav-users">
                <a class="menu-link" href="{{ route('admin.user.index') }}">
                    <img src="{{asset('images/menu-icons/busts-in-silhouette-icon.svg')}}" alt="" class="menu-icon">
                    <span class="menu-title smini-hide">{{ __('Users') }}</span>
                </a>
            </li>

            <li class="nav-item" id="nav-translations">
                <a class="menu-link" href="{{ route('admin.translation.index') }}">
                    <img src="{{asset('images/menu-icons/white-flag-icon.svg')}}" alt="" class="menu-icon">
                    <span class="menu-title smini-hide">{{ __('Translations') }}</span>
                </a>
            </li>
            @endif

            @if ( !auth()->user()->isAdmin() )
                <li class="nav-item" id="nav-one-on-one">
                    <a class="menu-link" href="{{ route('client.1-1.index') }}">
                        <img src="{{asset('images/menu-icons/one-on-one-icon.svg')}}" alt="" class="menu-icon">
                        <span class="menu-title smini-hide">{{ __('1-on-1') }}</span>
                    </a>
{{--                    <a class="nav-main-link active" href="{{ route('client.1-1.index') }}">--}}
{{--                        <button type="button" class="btn rounded-pill btn-alt-secondary nav-main-link-icon">--}}
{{--                            <img src="{{asset('images/menu-icons/')}}" alt="">--}}
{{--                        </button>--}}
{{--                        <i class="nav-main-link-icon far fa-comments"></i>--}}
{{--                        <span class="nav-main-link-name">{{ __('1-on-1') }}</span>--}}
{{--                    </a>--}}
                </li>
                <li class="nav-item" id="nav-deals">
                    <a class="menu-link" href="{{ route('client.deal.index') }}">
                        <img src="{{asset('images/emoji-icons/handshake.svg')}}" alt="" class="menu-icon">
                        <span class="menu-title smini-hide">{{ __('Deals') }}</span>
                    </a>
{{--                    <a class="nav-main-link active" href="{{ route('client.deal.index') }}">--}}
{{--                        <button type="button" class="btn rounded-pill btn-alt-secondary nav-main-link-icon">--}}
{{--                            <i class="far fa-handshake"></i>--}}
{{--                        </button>--}}
{{--                        <i class="nav-main-link-icon far fa-handshake"></i>--}}
{{--                        <span class="nav-main-link-name">{{ __('Deals') }}</span>--}}
{{--                    </a>--}}
                </li>
                <li class="nav-main-item" id="nav-settings">
                    <a class="menu-link menu-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                        <img src="{{asset('images/menu-icons/settings-icon.svg')}}" alt="" class="menu-icon">
                        <span class="menu-title smini-hide">Settings</span>
                    </a>
{{--                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">--}}
{{--                        <button type="button" class="btn rounded-pill btn-alt-secondary nav-main-link-icon">--}}
{{--                            <img src="{{asset('images/menu-icons/settings-icon.svg')}}" alt="">--}}
{{--                        </button>--}}
{{--                        <i class="nav-main-link-icon si si-settings"></i>--}}
{{--                        <span class="nav-main-link-name">Settings</span>--}}
{{--                    </a>--}}
                    <ul class="nav-main-submenu">
                        <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ route('user.show',Auth::user()) }}">
                                <span class="nav-main-link-name">{{ __('Profile') }}</span>
                            </a>
                        </li>
                        <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ route('client.pipeline.index') }}">
                                <span class="nav-main-link-name">{{ __('Pipelines') }}</span>
                            </a>
                        </li>
                        @php
                            $organization = Auth::user()->organization();
                        @endphp
                        @if($organization)
                        <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ route('organization.edit', $organization->id ) }}">
                                <span class="nav-main-link-name">{{ __('Organization') }}</span>
                            </a>
                        </li>
                        @endif
                        <li class="nav-main-item">
                            <a class="nav-main-link" href="{{ route('client.member.index') }}">
                                <span class="nav-main-link-name">{{ __('Members') }}</span>
                            </a>
                        </li>
{{--                        <li class="nav-main-item">--}}
{{--                            <a class="nav-main-link" href="#">--}}
{{--                                <span class="nav-main-link-name">{{ __('Billing') }}</span>--}}
{{--                            </a>--}}
{{--                        </li>--}}

                    </ul>
                </li>
            @endif

            <!--
            <li class="nav-main-heading">Heading</li>
            <li class="nav-main-item">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                    <i class="nav-main-link-icon si si-puzzle"></i>
                    <span class="nav-main-link-name">Dropdown</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="javascript:void(0)">
                            <span class="nav-main-link-name">Link #1</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="javascript:void(0)">
                            <span class="nav-main-link-name">Link #2</span>
                        </a>
                    </li>
                </ul>
            </li>
            -->

        </ul>
      </div>
      <!-- END Side Navigation -->
    </div>
    <!-- END Sidebar Scrolling -->
    </nav>
    <!-- END Sidebar -->
