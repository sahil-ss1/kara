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
    <meta property="og:title" content="{{ config('app.name', 'Laravel') }}">
    <meta property="og:site_name" content="kara.ai">
    <meta property="og:description" content="Kara">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://kara.ai">
    <meta property="og:image" content="">

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" sizes="32x32" href="{{ url('/') }}/images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ url('/') }}/images/favicons/favicon-192x192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/') }}/images/favicons/favicon-180x180.png">
    <!-- END Icons -->

    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="{{ url('/') }}/libs/select2/select2.min.css">

    <!-- Datatable stylesheet -->
    <!--<link rel="stylesheet" type="text/css" href="{{ url('/') }}/libs/datatables/datatables.min.css">-->
    <link rel="stylesheet" href="{{ url('/') }}/theme/js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/theme/js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/theme/js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/libs/datatables/dataTables.dateTime.min.css" />
    <link rel="stylesheet" type="text/css" href="{{ url('/') }}/libs/editor/css/editor.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.5.0/css/select.bootstrap5.min.css">

    <!-- Filepond stylesheet -->
    <link rel="stylesheet" type="text/css" href="{{ url('/') }}/libs/filepond/filepond.css">
    <link rel="stylesheet" type="text/css" href="{{ url('/') }}/libs/filepond/filepond-plugin-media-preview.css">
    <link rel="stylesheet" type="text/css" href="{{ url('/') }}/libs/filepond/filepond-plugin-image-preview.css">

    <link rel="stylesheet" href="{{ url('/') }}/libs/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/theme/js/plugins/ion-rangeslider/css/ion.rangeSlider.css">
    <!-- OneUI framework -->
    <link rel="stylesheet" id="css-main" href="{{ url('/') }}/theme/css/oneui.min.css">

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/custom.css">
    @yield('styles')
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/amethyst.min.css"> -->
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
<div id="page-container" class="sidebar-o sidebar-light enable-page-overlay side-scroll page-header-fixed main-content-narrow">

    <!-- Side Overlay-->
    @include('inc.rightside')
    <!-- END Side Overlay -->

    <!-- Sidebar -->
    @include('inc.sidenav')
    <!-- END Sidebar -->

    <!-- Header -->
    @include('inc.header')
    <!-- END Header -->

    <!-- Main Container -->
    <main id="main-container">
        <!-- Hero -->
        <div class="bg-body-light">
            <div class="content content-full" style="display: none">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
                    <div class="flex-grow-1">
                        <h1 class="h3 fw-bold mb-2" id="page_title"></h1>
                        <h2 class="fs-base lh-base fw-medium text-muted mb-0" id="page_subtitle"></h2>
                    </div>
                    <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-alt"></ol>
                    </nav>
                </div>
            </div>
        </div>
        <!-- END Hero -->

        <!-- Page Content -->
        <div class="content">
            @yield('content')
        </div>
        <!-- END Page Content -->
    </main>
    <!-- END Main Container -->

    <!-- Footer -->
    <footer id="page-footer" class="bg-body-light">
        <div class="content py-3">
            <div class="row fs-sm">
                <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
                    Developed by<!-- <i class="fa fa-heart text-danger"></i>--> <a class="fw-semibold" href="https://www.creativa-studio.eu/" target="_blank">Creativa</a>
                </div>
                <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
                    <a class="fw-semibold" href="#" target="_blank">{{ config('app.name', 'Laravel') }} v{{ config('app.ver') }}</a> &copy; <span data-toggle="year-copy"></span>
                    <span class="mx-2">|</span>
                    <a class="fw-semibold" href="{{ route('docs.terms-of-service') }}" target="_blank">Terms</a>
                    <span class="mx-1">·</span>
                    <a class="fw-semibold" href="{{ route('docs.privacy-policy') }}" target="_blank">Privacy</a>
                    <span class="mx-1">·</span>
                    <a class="fw-semibold" href="{{ route('docs.security-policy') }}" target="_blank">Security</a>
                </div>
            </div>
        </div>
    </footer>
    <!-- END Footer -->
</div>
<!-- END Page Container -->

<!--
    OneUI JS

    Core libraries and functionality
    webpack is putting everything together at assets/_js/main/app.js
-->
<script src="{{ url('/') }}/theme/js/oneui.app.min.js"></script>
<script src="{{ url('/') }}/libs/jquery-3.6.1.min.js"></script>
<script src="{{ url('/') }}/theme/js/plugins/bootstrap-notify/bootstrap-notify.min.js"></script>
<script src="{{ url('/') }}/theme/js/plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
<script src="{{ url('/') }}/libs/jquery.timeago.js"></script>
<script src="{{ url('/') }}/libs/moment.min.js"></script>

<script src="{{ url('/') }}/libs/sweetalert2/sweetalert2.min.js"></script>
<script src="{{ url('/') }}/libs/select2/select2.full.min.js"></script>

<script src="{{ url('/') }}/libs/datatables/datatables.min.js"></script>
<script src="{{ url('/') }}/libs/datatables/ellipsis.js"></script>
<script src="{{ url('/') }}/libs/editor/js/dataTables.editor.min.js"></script>
<script src="{{ url('/') }}/libs/editor/js/editor.bootstrap5.min.js"></script>
<script src="{{ url('/') }}/libs/editor/js/editor.select2.js"></script>
<script src="{{ url('/') }}/libs/editor/js/editor.readonly.js"></script>

<!-- include FilePond library -->
<script src="{{ url('/') }}/libs/filepond/filepond.min.js"></script>
<script src="{{ url('/') }}/libs/filepond/filepond-plugin-file-metadata.js"></script>
<script src="{{ url('/') }}/libs/filepond/filepond-plugin-file-validate-size.js"></script>
<script src="{{ url('/') }}/libs/filepond/filepond-plugin-file-validate-type.js"></script>
<script src="{{ url('/') }}/libs/filepond/filepond-plugin-media-preview.js"></script>
<script src="{{ url('/') }}/libs/filepond/filepond-plugin-image-preview.js"></script>

<!-- Page JS Helpers (BS Notify Plugin) -->
<script>One.helpersOnLoad(['jq-notify']);</script>

<script src="{{ url('/') }}/js/custom-script.js"></script>
<script>
    @if ( config('app.notifications') )
    $('#page-header-notifications-dropdown').on('click', function(e){
        fillNotifications('{{ route('user.notifications') }}', '{{ url('/') }}');
    })
    @endif

    //flash('My message', 'info'); //info,success,warning,danger
    @if(flash()->message)
        One.helpers('jq-notify', {type: '{{ flash()->class }}', icon: 'fa fa-check me-1', align: 'center', message: '{{ flash()->message }}'});
    @endif

    $(document).ready(function() {
        $("html").tooltip({ selector: '[data-toggle=tooltip]' }).popover({ selector: '[data-toggle=popover]' });
    });

    $(document).on('click', function (e) {
        $('[data-toggle="popover"],[data-original-title]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
            }

        });
    });

    //window.livewire.onError(statusCode => {
    //    if (statusCode === 419) {
            //alert('Your own message')
            //return false
    //    }
    //})
</script>

@stack('scripts')
@yield('scripts')
@stack('components_scripts')
</body>
</html>
