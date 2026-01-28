<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="The all-in-one platform to scale your Sales Teams">
    <meta name="author" content="Dimitris Gkemitzis">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Open Graph Meta -->
    <meta property="og:title" content="Lara Template">
    <meta property="og:site_name" content="Lara Template">
    <meta property="og:description" content="The all-in-one platform to scale your Sales Teams">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url', '') }}">
    <meta property="og:image" content="">

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="theme/media/favicons/favicon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="theme/media/favicons/favicon-192x192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="theme/media/favicons/apple-touch-icon-180x180.png">
    <!-- END Icons -->

    <!-- Stylesheets -->
    <!-- OneUI framework -->
    <link rel="stylesheet" id="css-main" href="theme/css/oneui.min.css">

    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/amethyst.min.css"> -->

    @yield('styles')
    <!-- END Stylesheets -->
</head>
<body>
    <!-- Page Container -->
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
    <div id="page-container" class="sidebar-dark side-scroll page-header-fixed page-header-dark main-content-boxed">

        @include("inc.frontend.sidebar")
        @include("inc.frontend.header")

        <!-- Main Container -->
        <main id="main-container">
            @yield('content')
        </main>

        <!-- Footer -->
        @include("inc.frontend.footer")
        <!-- END Footer -->

    </div>
    <!-- END Page Container -->


    <!--
        OneUI JS
        Core libraries and functionality
        webpack is putting everything together at assets/_js/main/app.js
    -->
    <script src="theme/js/oneui.app.min.js"></script>
    @yield('scripts')
</body>
</html>
