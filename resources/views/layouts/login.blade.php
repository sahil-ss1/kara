<!doctype html>
<html class="loading" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-textdirection="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="">
    <meta name="author" content="creativa.gr">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Open Graph Meta -->
    <meta property="og:title" content="{{ config('app.name', 'Laravel') }}">
    <meta property="og:site_name" content="">
    <meta property="og:description" content="The all-in-one platform to scale your Sales Teams">
    <meta property="og:type" content="website">
    <meta property="og:url" content="">
    <meta property="og:image" content="">

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/') }}/images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/') }}/images/favicons/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/') }}/images/favicons/apple-touch-icon.png">
    <!-- END Icons -->

    <!-- Stylesheets -->
    <!-- OneUI framework -->
    <link rel="stylesheet" id="css-main" href="{{ url('/') }}/theme/css/oneui.min.css">

    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/amethyst.min.css"> -->
      <style>
          body {
              background-color: white;
          }
          .login-logo {
              display: flex;
              justify-content: center;
              position: fixed;
              top: 60px;
          }
          .block {
              background: #f7f8fb;
          }
          .block.block-rounded {
              border-radius: 12px;
          }
          .el-button {
              line-height: 1;
              white-space: nowrap;
              cursor: pointer;
              background: #fff;
              text-align: center;
              box-sizing: border-box;
              outline: none;
              margin: 0;
              transition: .1s;
              font-weight: 600;
              border-radius: 15px;
          }
          .hubspot-button {
              display: flex;
              align-items: center;
              justify-content: center;
              max-height: 46px;
              background-color: #ff7b31;
              border: 2px solid #ff7b31!important;
              color: #fff;
              width: 240px;
          }
          .hubspot-button:hover {
              background-color: #ffbc96;
              border-color: #ff7b31!important;
              color: #fff;
          }
          .credentials-button {
              display: flex;
              align-items: center;
              justify-content: center;
              max-height: 46px;
              background-color: #313131;
              border: 2px solid #313131!important;
              color: #fff;
              width: 240px;
          }
          .credentials-button:focus, .credentials-button:hover {
              background: #5a5a5a;
              border-color: #5a5a5a;
              color: #fff;
          }
          #login .k-demo-book {
              color: #3b43ed;
              font-size: 12px;
              font-weight: 700;
              display: flex;
          }
          .el-button--info {
              color: #fff;
              background-color: #3b43ed;
              border-color: #3b43ed;
          }
          .el-button--info:focus,.el-button--info:hover {
              background: #6269f1;
              border-color: #6269f1;
              color: #fff
          }
          .el-button--small {
              padding: 9px 15px;
              font-size: 12px;
          }
          input[type="text"].form-control, input[type="email"].form-control, input[type="number"].form-control, input[type="password"].form-control {
              border: 2px solid #f7f8fb;
              background-color: #fff;
              font-size: 16px;
              border-radius: 20px !important;
              line-height: 22px;
              padding: 12px 16px;
              display: block;
              box-sizing: border-box;
              width: 100%;
              color: #313131;
              transition: border-color .2s cubic-bezier(.645,.045,.355,1);
              font-family: "SF Compact Text", Inter, serif;
          }
          .hero-static {
              background: url(../images/login_bg.1eaaec66.svg) 0 0 no-repeat;
              background-size: cover;
              background-position: bottom;
          }
      </style>
      @yield('styles')
    <!-- END Stylesheets -->

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </head>

  <body>
      <!--
        Available classes for #page-container:

    GENERIC

      'remember-theme'                            Remembers active color theme and dark mode between pages using localStorage when set through
                                                  - Theme helper buttons [data-toggle="theme"],
                                                  - Layout helper buttons [data-toggle="layout" data-action="dark_mode_[on/off/toggle]"]
                                                  - ..and/or One.layout('dark_mode_[on/off/toggle]')

    SIDEBAR & SIDE OVERLAY

      'sidebar-r'                                 Right Sidebar and left Side Overlay (default is left Sidebar and right Side Overlay)
      'sidebar-mini'                              Mini hoverable Sidebar (screen width > 991px)
      'sidebar-o'                                 Visible Sidebar by default (screen width > 991px)
      'sidebar-o-xs'                              Visible Sidebar by default (screen width < 992px)
      'sidebar-dark'                              Dark themed sidebar

      'side-overlay-hover'                        Hoverable Side Overlay (screen width > 991px)
      'side-overlay-o'                            Visible Side Overlay by default

      'enable-page-overlay'                       Enables a visible clickable Page Overlay (closes Side Overlay on click) when Side Overlay opens

      'side-scroll'                               Enables custom scrolling on Sidebar and Side Overlay instead of native scrolling (screen width > 991px)

    HEADER

      ''                                          Static Header if no class is added
      'page-header-fixed'                         Fixed Header

    HEADER STYLE

      ''                                          Light themed Header
      'page-header-dark'                          Dark themed Header

    MAIN CONTENT LAYOUT

      ''                                          Full width Main Content if no class is added
      'main-content-boxed'                        Full width Main Content with a specific maximum width (screen width > 1200px)
      'main-content-narrow'                       Full width Main Content with a percentage width (screen width > 1200px)

    DARK MODE

      'sidebar-dark page-header-dark dark-mode'   Enable dark mode (light sidebar/header is not supported with dark mode)
    -->
    <div id="page-container">
      <!-- Main Container -->
        <main id="main-container">
            <!-- Page Content -->
            <div class="hero-static d-flex align-items-center">
                <div class="content">
                    <div class="row justify-content-center push">
                        <div class="login-logo">
                            <img src="{{asset('images/menu-icons/logo-kara-full.svg')}}" alt="">
                        </div>
                        @yield('content')
                    </div>
                    <div class="fs-sm text-muted text-center">
                        <strong>Creativa v{{ config('app.ver') }}</strong> &copy; <span data-toggle="year-copy"></span>
                    </div>
                </div>
            </div>
        </main>
      <!-- END Main Container -->
    </div>
    <!-- END Page Container -->


    <script src="{{ url('/') }}/theme/js/oneui.app.min.js"></script>
      <script src="{{ url('/') }}/libs/jquery-3.6.1.min.js"></script>
    <!-- jQuery (required for jQuery Validation plugin) -->
    <!-- <script src="{{ url('/') }}/theme/js/lib/jquery.min.js"></script> -->
    <!-- Page JS Plugins -->
    <!--<script src="{{ url('/') }}/theme/js/plugins/jquery-validation/jquery.validate.min.js"></script>-->

    @yield('scripts')
  </body>
</html>
