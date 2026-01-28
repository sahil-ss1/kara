@extends('layouts.app')

@section('content')
    <!-- Your Block -->
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                Title <small>Get Started</small>
            </h3>
            <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="pinned_toggle">
                    <i class="si si-pin"></i>
                </button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                    <i class="si si-refresh"></i>
                </button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="close">
                    <i class="si si-close"></i>
                </button>
            </div>
        </div>
        <div class="block-content fs-sm">
            <p>
                Create your own awesome project!
            </p>
        </div>
    </div>
    <!-- END Your Block -->
@endsection

@section('scripts')
    <script>
        setPageTitle('Page Title', 'Subtitle');
        addBreadcrumbItem('Dashboard', null);
    </script>
@endsection
