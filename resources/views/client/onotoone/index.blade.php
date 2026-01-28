@extends('layouts.app')

@section('content')
    <div id="one-on-one">
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="row">
                    <div class="section-row">
                        <label class="kara-page-title" for="teams-select">{{ __('1-on-1\'s') }}</label>
                        <div class="team-select">
                            <select class="form-select" id="teams-select">
                                <option></option>
                                @foreach( $teams as $key=>$value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(!Auth::user()->google_token)
                            <div class="ms-4">
                                <button type="button" class="tooltip-button-transparent" data-toggle="tooltip" data-bs-custom-class="warning-calendar-tooltip" data-bs-html="true" data-bs-placement="right"
                                        title="<strong>Connect Google Calendar</strong> <br> For a smoother experience, we recommend that you connect your Google Calendar.">
                                    <i class="fa fa-exclamation-triangle warning-icon"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6" style="text-align: right">
                <div>
                    <a id='goal-btn' class="link-text-btn" href="{{ route('client.meeting.index') }}">{{ __('History') }}</a>
                    <button type="button" class="btn btn-primary btn-round js-click-ripple-enabled" onclick="window.location.href='{{ route('client.meeting.index') }}'">
                        <i class="fa fa-sticky-note"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div>
                <div class="table-responsive">
                    <table class="table table-hover table-borderless table-vcenter" id="teamMembersTbl">
                        <thead style="display:none">
                        <tr>
                            <th>ID</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Schedule') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class='empty-table-block' id="teamMembersTblEmpty">
                        <div class='empty-table'>
                            <span class='empty-table-text'>There is nothing here</span>
                            <img width='21' height='21' src='{{asset('images/emoji-icons/neutral.svg')}}' alt=''>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        setPageTitle('1-on-1\'s');
        addBreadcrumbItem('1-on-1\'s', null);

        var team_select = $("#teams-select").select2({
            placeholder: 'Select teams',
            dropdownAutoWidth: true,
            width: '100%',
            minimumResultsForSearch: 20, // at least 20 results must be displayed
            dropdownCssClass: 'select-team-dropdown'
        })
        .on('change', function (e) {
            let selectedValue = $("#teams-select").select2("val");
            // Extract the actual value - select2("val") returns a string for single select
            let teamId = selectedValue || null;
            
            if (teamId) {
                // Save as array for localStorage
                let data = [teamId];
            saveStateLocal('1to1-team-selection', data);
                // Use the teamId directly (not the array) in the URL
                $("#teamMembersTbl").DataTable().ajax.url('{{ url('/').'/client/1-1/team/'}}'+teamId+'/members/datatable').load();
            } else {
                // If no team selected, clear the table or show empty state
                $("#teamMembersTbl").DataTable().ajax.url('{{ url('/').'/client/1-1/team/0/members/datatable'}}').load();
            }
        });

        $('.team-select>.select2-container').addClass('select-team');


        let tableTeamMembers = $('#teamMembersTbl').DataTable({
            ajax: {
                url: "{{ url('/').'/client/1-1/team/0/members/datatable'}}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            },
            dom:"<'row'<'col-sm-12 col-md-8'l><'col-sm-12 col-md-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            serverSide: true,
            pageLength: 20,
            responsive: false,
            paging: false,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: false,
            autoWidth: false,
            destroy: true,
            scrollY: "68vh",
            scrollX: true,
            scrollCollapse: true,
            language: {
                search: "",
                searchPlaceholder: "Search Members"
            },
            columns: [
                {data: "id", visible: false, width: '100px' },
                {data: 'lastName', className: 'name-column', render: function (val, type, row) {
                        if (type === 'display') {
                            return '<div class="member-tbl-line">' +
                                '<a href="{{ url('/') }}/client/profile/'+row['id']+'">' +
                                '<div class="v-avatar member-avatar me-2" style="height: 38px; min-width: 38px; width: 38px; margin: 0">' +
                                '<span>' + row['firstName'][0] + val[0] + '</span>' +
                                '</div></a>' + row['firstName'] + ' ' + val +
                                '</div>';
                        }
                        return val;
                    }
                },
                {data: 'email'},
                {
                    data: null,
                    className: "text-center",
                    render: function (val, type, row) {
                        if (row['meet']&&row['email'])
                            if (row['google_event_id'])
                                return '<button type="button" class="btn btn-outline-orange btn-sm editor_schedule_edit" style="border-radius: 12px" {{ !Auth::user()->google_token ? 'disabled' : '' }}>{{__('Scheduled')}}</button>'
                            else
                                return '<button type="button" class="btn btn-outline-orange btn-sm editor_schedule" style="border-radius: 12px" {{ !Auth::user()->google_token ? 'disabled' : '' }}>{{__('Schedule')}}</button>'
                        else return '';
                    },
                    responsivePriority: 1,
                    sortable: false,
                    searchable: false
                },
                {
                    data: 'meet',
                    className: "text-center p-0",
                    render: function (val, type, row) {
                    if (type === 'display') {
                        let html = '<div class="btn-group">';
                        if (val) html+= '<button type="button" class="btn btn-outline-primary btn-sm editor_open_meet"  style="border-radius: 12px; padding: 4px 20px">{{__('Instant 1-on-1')}} <i class="fa-solid fa-arrow-right ps-1"></i></button>';
                        else html+= '<button type="button" class="btn btn-outline-primary btn-sm editor_create_meet"  style="border-radius: 12px; padding: 4px 20px"><i class="fa-solid fa-plus pe-1"></i> {{__('Instant 1-on-1')}}</button>';
                        html+='</div>';
                        return html;

                    }
                      return val;
                    },
                    responsivePriority: 1,
                    sortable: false,
                    searchable: false
                }
            ],
            order: [[1, 'asc']],
            initComplete: function () {
                let html = '<div class="one-on-one-heading-title">{{ __('Players') }}</div>';
                $(html).appendTo( $('.col-md-8:eq(0)', '#teamMembersTbl_wrapper.dataTables_wrapper' ) );
            },
            drawCallback: function() {
                if ($('#teamMembersTbl').DataTable().data().count() > 0) {
                    $('tbody').css('display', 'table-row-group');
                    $("#teamMembersTblEmpty").css('display', 'none');
                } else {
                    $('tbody').css('display', 'none');
                    $("#teamMembersTblEmpty").css('display', 'flex');
                }
            },
        }).on('click', 'button.editor_open_meet', function (e) {// Delete a record
            e.preventDefault();

            let data = tableTeamMembers.row( $(this).closest('tr') ).data();
            let id = data["meet"];

            window.location.href = '{{ url('/').'/client/meeting/' }}'+ id +'/edit';
        }).on('click', 'button.editor_create_meet', function (e) {
            e.preventDefault();

            let data = tableTeamMembers.row( $(this).closest('tr') ).data();
            let member = data['id'];

            let form_data = new FormData();
            form_data.append('target_id', member);
            submitAjaxForm(
                '{{ url('/').'/client/meeting' }}',
                form_data,
                (result) => { window.location.href = '{{ url('/').'/client/meeting/' }}'+ result +'/edit'; }
            )
        }).on('click', 'button.editor_schedule', function (e) {
            e.preventDefault();

            let data = tableTeamMembers.row( $(this).closest('tr') ).data();
            let id = data["meet"];

            createModal(
                '{{ __('1-to-1 Schedule') }}',
                null,
                '{{ url('/') }}/client/meeting/'+id+'/schedule',
                'modal-header-orange',
                null,
                function(modal) {
                    submitAjaxFormWithValidation(
                        $('#form-schedule'),
                        function(e) {
                            modal.hide();
                            $('#teamMembersTbl').DataTable().ajax.reload();
                        }
                    )
                },
                null
            )
        }).on('click', 'button.editor_schedule_edit', function (e) {
            e.preventDefault();

            let data = tableTeamMembers.row( $(this).closest('tr') ).data();
            let id = data["meet"];

            createModal(
                '{{ __('1-to-1 Schedule') }}',
                null,
                '{{ url('/') }}/client/meeting/'+id+'/schedule/edit',
                'modal-header-orange',
                null,
                null,
                null
            )
        });

        $(document).ready(function() {
            $('div#teamMembersTbl_filter input').addClass('search-input');
            let search_icon = '<span onclick="openOneOnOneSearchBar()" id="search-icon" class="search-icon"><a type="button"><i class="fa fa-search"></i></a></span>';
            $('div#teamMembersTbl_filter').prepend(search_icon);
        });

        function openOneOnOneSearchBar() {
            $('div#teamMembersTbl_filter').addClass('expand');
            $('div#teamMembersTbl_filter input').addClass('search-input-open').focus();
            $('#search-icon').addClass('search-icon-open');
        }

        $(document).on('click', function () {
            if (!$('div#teamMembersTbl_filter input').is(":focus")) {
                $('div#teamMembersTbl_filter').removeClass('expand');
                $('div#teamMembersTbl_filter input').removeClass('search-input-open');
                $('#search-icon').removeClass('search-icon-open');
            }
        });

        $(function() {
            let savedTeam = getStateLocal('1to1-team-selection');
            if (savedTeam) {
                // Handle both array and string formats
                let teamId = Array.isArray(savedTeam) ? savedTeam[0] : savedTeam;
                if (teamId) {
                    team_select.val(teamId).trigger('change');
                }
            }
        });
    </script>
@endsection
