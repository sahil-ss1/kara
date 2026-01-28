@extends('layouts.app')

@section('content')
    <form action="{{ route('organization.update', $organization) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-block title="My Organization" >
            <div class="row">
                <div class="col">
                    <div class="col-4">
                        <div class="mb-4">
                            <label class="form-label" for="name">{{ __('Organization name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Organization name" value="{{ $organization->name }}" required readonly>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-4">
                            <label class="form-label" for="currency">{{ __('Organization default currency') }}</label>
                            <div class="default-select">
                                <select name="currency" class="form-control" id="organization-default-currency-select">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency['code'] }}" {{ ($currency['code'] == $organization->currency)?'selected':''  }} > {{ $currency['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="col-4" style="padding: 0">
                        <div class="block block-mode-hidden deal-related">
                            <div class="heading">
                                <h3 class="block-title" style="font-size: 16px">Warnings threshold</h3>
                                <div class="block-options">
                                    <button type='button' class="btn btn-outline-secondary btn-round" data-toggle='block-option' data-action='content_toggle'></button>
                                </div>
                            </div>
                            <div class="block-content">
                                <div class="col mb-4">
                                    <label class="form-label" for="warn_last_activity_days">{{ __('Last activity warning') }}</label>
                                    <input type="number" class="form-control" id="warn_last_activity_days" name="warn_last_activity_days" placeholder="Days" value="{{ $organization->warn_last_activity_days }}" required>
                                </div>
                                <div class="col mb-4">
                                    <label class="form-label" for="warn_stage_time_days">{{ __('Days spend in a stage warning') }}</label>
                                    <input type="number" class="form-control" id="warn_stage_time_days" name="warn_stage_time_days" placeholder="Days" value="{{ $organization->warn_stage_time_days }}" required>
                                </div>
                                <div class="col mb-4">
                                    <label class="form-label" for="warn_creation_time_days">{{ __('Days from creation warning') }}</label>
                                    <input type="number" class="form-control" id="warn_creation_time_days" name="warn_creation_time_days" placeholder="Days" value="{{ $organization->warn_creation_time_days }}" required>
                                </div>
                            </div>
                        </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 col-xl-5">
                    <div class="mb-4">
                        <button type="submit" class="el-button el-button--info">
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </div>
        </x-block>
    </form>
@endsection

@section('scripts')
    <script>
        setPageTitle('{{ __('Organization') }}');
        addBreadcrumbItem('{{ __('Organization') }}', null);

        $("#organization-default-currency-select").select2({
            placeholder: 'Select organization default currency',
            dropdownAutoWidth: true,
            width: '100%',
            multiple: false,
            minimumResultsForSearch: -1,
            dropdownCssClass: 'select-default-dropdown'
        })

        $('.default-select>.select2-container').addClass('select-default');

    </script>
@endsection
