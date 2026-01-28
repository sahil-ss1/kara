@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-5 col-xl-3">
        <x-block title="Notifications">
            <livewire:show-notifications />
        </x-block>
    </div>

    <div class="col-md-7 col-xl-9" id="notification-content">
        <div></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    setPageTitle('{{ __('Notifications') }}');
    addBreadcrumbItem('{{ __('Notifications') }}', null);

    $('.notification_item').on('click', function (e){
        let id = $(this).data('id');
        $('#notification-content').load('{{ url('/').'/notification/' }}'+id+'/details');
    })

    $(function () {
        @isset( $notification )
            $("a[data-id='{{ $notification->id }}']").click();
        @endisset
    });
</script>
@endsection
