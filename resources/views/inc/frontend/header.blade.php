<!-- Header -->
<header id="page-header">
    <!-- Header Content -->
    <div class="content-header">
        <!-- Left Section -->
        <div class="d-flex align-items-center">
            <!-- Logo -->
            <a class="fw-semibold fs-5 tracking-wider text-dual me-3" href="index.html">
                One<span class="fw-normal">UI</span>
            </a>
            <!-- END Logo -->
        </div>
        <!-- END Left Section -->

        <!-- Right Section -->
        <div class="d-flex align-items-center">
            <!-- Menu -->
            <div class="d-none d-lg-block">
                <ul class="nav-main nav-main-horizontal nav-main-hover">
                    <li class="nav-main-item">
                        <a class="nav-main-link active" href="gs_landing.html">
                            <i class="nav-main-link-icon si si-home"></i>
                            <span class="nav-main-link-name">Home</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="javascript:void(0)">
                            <i class="nav-main-link-icon si si-rocket"></i>
                            <span class="nav-main-link-name">Features</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="javascript:void(0)">
                            <i class="nav-main-link-icon si si-wallet"></i>
                            <span class="nav-main-link-name">Pricing</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="javascript:void(0)">
                            <i class="nav-main-link-icon si si-envelope"></i>
                            <span class="nav-main-link-name">Contact</span>
                        </a>
                    </li>

                    <li class="nav-main-heading">Extra</li>
                    <li class="nav-main-item">
                        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                            <i class="nav-main-link-icon fa fa-brush"></i>
                        </a>
                        <ul class="nav-main-submenu nav-main-submenu-right">
                            <li class="nav-main-item">
                                <a class="nav-main-link" data-toggle="theme" data-theme="default" href="#">
                                    <i class="nav-main-link-icon fa fa-square text-default"></i>
                                    <span class="nav-main-link-name">Default</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" data-toggle="theme" data-theme="theme/css/themes/amethyst.min.css" href="#">
                                    <i class="nav-main-link-icon fa fa-square text-amethyst"></i>
                                    <span class="nav-main-link-name">Amethyst</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" data-toggle="theme" data-theme="theme/css/themes/city.min.css" href="#">
                                    <i class="nav-main-link-icon fa fa-square text-city"></i>
                                    <span class="nav-main-link-name">City</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" data-toggle="theme" data-theme="theme/css/themes/flat.min.css" href="#">
                                    <i class="nav-main-link-icon fa fa-square text-flat"></i>
                                    <span class="nav-main-link-name">Flat</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" data-toggle="theme" data-theme="theme/css/themes/modern.min.css" href="#">
                                    <i class="nav-main-link-icon fa fa-square text-modern"></i>
                                    <span class="nav-main-link-name">Modern</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" data-toggle="theme" data-theme="theme/css/themes/smooth.min.css" href="#">
                                    <i class="nav-main-link-icon fa fa-square text-smooth"></i>
                                    <span class="nav-main-link-name">Smooth</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    @if (Route::has('login'))
                        @auth
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="{{ url('/home') }}">
                                    <i class="nav-main-link-icon si si-envelope"></i>
                                    <span class="nav-main-link-name">Dashboard</span>
                                </a>
                            </li>
                        @else
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="{{ route('login') }}">
                                    <i class="nav-main-link-icon si si-envelope"></i>
                                    <span class="nav-main-link-name">Log in</span>
                                </a>
                            </li>

                            @if (Route::has('register'))
                                <li class="nav-main-item">
                                    <a class="nav-main-link" href="{{ route('register') }}">
                                        <i class="nav-main-link-icon si si-envelope"></i>
                                        <span class="nav-main-link-name">Register</span>
                                    </a>
                                </li>
                            @endif
                        @endauth
                    @endif

                </ul>
            </div>
            <!-- END Menu -->

            <!-- Toggle Sidebar -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <button type="button" class="btn btn-sm btn-alt-secondary d-lg-none ms-1" data-toggle="layout" data-action="sidebar_toggle">
                <i class="fa fa-fw fa-bars"></i>
            </button>
            <!-- END Toggle Sidebar -->
        </div>
        <!-- END Right Section -->
    </div>
    <!-- END Header Content -->

    <!-- Header Search -->
    <div id="page-header-search" class="overlay-header bg-body-extra-light">
        <div class="content-header">
            <form class="w-100" method="POST">
                <div class="input-group input-group-sm">
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    <button type="button" class="btn btn-alt-danger" data-toggle="layout" data-action="header_search_off">
                        <i class="fa fa-fw fa-times-circle"></i>
                    </button>
                    <input type="text" class="form-control" placeholder="Search or hit ESC.." id="page-header-search-input" name="page-header-search-input">
                </div>
            </form>
        </div>
    </div>
    <!-- END Header Search -->

    <!-- Header Loader -->
    <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->
    <div id="page-header-loader" class="overlay-header bg-primary-lighter">
        <div class="content-header">
            <div class="w-100 text-center">
                <i class="fa fa-fw fa-circle-notch fa-spin text-primary"></i>
            </div>
        </div>
    </div>
    <!-- END Header Loader -->
</header>
<!-- END Header -->
