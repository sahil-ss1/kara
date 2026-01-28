@extends('layouts.app')

@section('content')
    @if(!Auth::user()->isAdmin() && !Auth::user()->hubspot_refreshToken)
    <div class="alert alert-warning alert-dismissible fade show" role="alert" style="margin-bottom: 20px;">
        <div class="d-flex align-items-center">
            <i class="fa-brands fa-hubspot fa-2x me-3" style="color: #ff7b31;"></i>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1">Connect Your HubSpot Account</h5>
                <p class="mb-0">Sync your CRM data, manage deals, and track team performance. <a href="{{ route('hubspot.install') }}" class="alert-link fw-bold">Connect now →</a></p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @endif
    
    <livewire:dashboard.dashboard />
    <x-block>
        <div class="row">
            <div class="col warnings">
                <x-dashboard.deals-widget />
            </div>
        </div>
    </x-block>
    <x-deals-grid tableid="modal-deals-grid"></x-deals-grid>
@endsection

@section('scripts')
    <script>
        setPageTitle('Dashboard');
        addBreadcrumbItem('Dashboard', null);
        $("div.content").addClass("dashboard-page");

    </script>
@endsection
