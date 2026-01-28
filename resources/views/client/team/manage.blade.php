<div>
    <div class="row">
        <div class="col-4">
            <div class="table-responsive" style="padding-top:5px; height: 60vh; overflow-y: auto">
                <table class="table table-hover table-borderless table-vcenter" id="membersTbl">
                    <thead style="display:none">
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>{{ __('Name') }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div style="padding-top:5px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                <div x-data="{
                                selectAll:false,
                                click(){
                                    this.selectAll=!this.selectAll;
                                    if (this.selectAll) $('#membersTbl .row_selector:not(:checked)').click()
                                    else $('#membersTbl .row_selector:checked').click();
                                }
                             }"
                >
                    <button type="button" id="select-all-btn" class="el-button outlined-button"
                            @click="click()"
                            x-text="selectAll?'{{ __('Deselect All') }}':'{{ __('Select All') }}'">
                    </button>
                </div>
                <button type="button" id="add-to-team-btn" class="el-button el-button--info" onclick="addMembers();" style="width:200px">{{ __('Add to team') }}</button>
            </div>

        </div>

        <div class="col-8">

            <div class="row" style="align-items:baseline">
                <div class="col-sm-4 manage-team-select">
                    <select class="form-select" id="manage-team-select"></select>
                </div>
                <div class="col-sm-8">
                    <button type="button" class="btn btn-outline-secondary btn-round me-2" id="manage-teams-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-ellipsis more-icon"></i>
                    </button>
                    <div class="dropdown-menu goal-dropdown fs-sm" aria-labelledby="manage-teams-dropdown">
                        <a class="dropdown-item goal-dropdown-item" onclick="editTeam()"><i class="fa fa-pencil goal-dropdown-icon"></i>Edit</a>
                        <a class="dropdown-item goal-dropdown-item" onclick="deleteTeam()"><i class="fa fa-trash goal-dropdown-icon"></i>Delete</a>
                    </div>
                    <a href="#" onclick="addNewTeam()"><i class="fa fa-fw fa-plus"></i>{{ __('Create another team') }}</a>
                </div>
            </div>

            <div id="team-members-tbl" class="table-responsive" style="padding-top:5px; height: 60vh; overflow-y: auto">
                <table class="table table-hover table-borderless table-vcenter" id="teamMembersTbl">
                    <thead style="display:none">
                    <tr>
                        <th>ID</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div id="no-team-members-block" class="select-people mt-3">
                <img src="data:image/png;base64,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" alt="">
                <span>{{ __('Add members of your team') }}</span>
            </div>

            <div id="please-select-team-block" class="select-people mt-3">
                <h3>{{ __('Please select team') }}</h3>
            </div>

        </div>
    </div>
</div>

<script>
    function getTeams(){
        $.get('{{ route('client.team.get') }}', function( data ) {
            let teams = JSON.parse(data);
            let sel = $('#manage-team-select');
            sel.empty();
            {{--sel.append($("<option>").attr('disabled',true).attr('selected',true).text('{{__('Please select team')}}'));--}}
            sel.append($("<option>"));
            Object.keys(teams).forEach(key => {
                if (key===getTeamSelectionFromLocalStorage()) {
                    sel.append($("<option>").attr('value',key).attr('selected',true).text(teams[key]));
                    $("#teamMembersTbl").DataTable().ajax.reload();
                } else {
                    sel.append($("<option>").attr('value',key).text(teams[key]));
                }
            });
            sel.val(getTeamSelectionFromLocalStorage());
            $('#manage-teams-block-subtitle').text('('+Object.keys(teams).length+')');

            if (!sel.val()) {
                $('#team-members-tbl').css('display', 'none');
                $('#manage-teams-dropdown').hide();
                $('#no-team-members-block').css('display', 'none');
                $('#please-select-team-block').css('display', 'flex');
                $('#select-all-btn').prop('disabled', true );
                $('#add-to-team-btn').prop('disabled', true );
            }
        });

        let manage_team_select = $("#manage-team-select").select2({
            placeholder: 'Please select team',
            dropdownAutoWidth: true,
            width: '100%',
            multiple: false,
            minimumResultsForSearch: -1,
            dropdownCssClass: 'select-team-dropdown'
        })
            .on('change', function (e) {
                let data = $("#manage-team-select").select2("val");
                if (data) saveStateLocal('team-selection-change', data);
              //  $("#teams-select").val(getTeamSelectionFromLocalStorage()).trigger('change');
              //  document.getElementById('deals-overview').dispatchEvent(new CustomEvent('refresh-teams', { detail: {}}));
              //  createDealsMembersSelect();
                $("#teamMembersTbl").DataTable().ajax.reload();
                $('#manage-teams-dropdown').show();
                $('#please-select-team-block').css('display', 'none');
                $('#select-all-btn').prop('disabled', false );
                $('#add-to-team-btn').prop('disabled', false );
            })
            .val(getTeamSelectionFromLocalStorage())
            .trigger('change');

        $('.manage-team-select>.select2-container').addClass('select-team');
    }

    function addNewTeam(){
        {{--Swal.fire({--}}
        {{--    title: 'Create a team',--}}
        {{--    inputLabel: 'What\'s your team name',--}}
        {{--    inputPlaceholder: 'Enter your team\'s name',--}}
        {{--    input: 'text',--}}
        {{--    showCancelButton: true,--}}
        {{--    target: '#modal',--}}
        {{--    preConfirm: (name) => {--}}
        {{--        let form_data = new FormData();--}}
        {{--        form_data.append('name', name);--}}
        {{--        submitAjaxForm(--}}
        {{--            '{{ route('client.team.store') }}',--}}
        {{--            form_data,--}}
        {{--            (result) => { getTeams(); }--}}
        {{--        )--}}
        {{--    }--}}
        {{--})--}}
        createModalOverModal('Create a team',
            null,
            '{{ route('client.team.create') }}',
            'modal-lg modal-footer-transparent',
            null,
            function (modal) {
                submitAjaxFormWithValidation(
                    $('#form-team-create'),
                    function(result) {
                        getTeams();
                        modal.hide();
                    }
                );
            },
            null,
            '1062',
            '5.4rem 0 0 0',
            'Cancel',
            'Submit'
        );
    }

    function editTeam(){
        $.get('{{ route('client.team.get') }}', function( data ) {
            let teams = JSON.parse(data);
            let teamId = $('#manage-team-select').val();
            createModalOverModal('Edit this team: ' + teams[teamId],
                null,
                '{{ url('/') }}/client/team/'+teamId+'/edit',
                'modal-lg modal-footer-transparent',
                null,
                function (modal) {
                    submitAjaxFormWithValidation(
                        $('#form-team-edit'),
                        function(result) {
                            getTeams();
                            modal.hide();
                        }
                    )
                },
                null,
                '1062',
                '5.4rem 0 0 0',
                'Cancel',
                'Submit'
            );
        });
    }

    function deleteTeam(){
        $.get('{{ route('client.team.get') }}', function( data ) {
            let teams = JSON.parse(data);
            let teamId = $('#manage-team-select').val();
            createModalOverModal('Delete ' + teams[teamId] + '?',
                '{{ __('This will permanently delete this team. Continue?') }}',
                null,
                'modal-lg modal-footer-transparent',
                null,
                function (modal) {
                    let form_data = new FormData();
                    form_data.append('_method', 'DELETE');
                    let action = '{{ url('/') }}/client/team/'+teamId
                    submitAjaxForm(
                        action,
                        form_data,
                        function(e) {
                            getTeams();
                            modal.hide();
                        }
                    )
                },
                null,
                '1062',
                '5.4rem 0 0 0',
                null,
                null
            );
        });
    }

    function addMembers(){
        let rows = tableMembers.rows( { selected: true } ).data();
        let ids=[]; let teamid = $('#manage-team-select').val();
        rows.each(function(row){
            ids.push(row['id']);
        });
        
        if (ids.length === 0) {
            alert('Please select at least one member to add.');
            return;
        }
        
        let form_data = new FormData();
        // Append each member ID individually so Laravel receives it as an array
        ids.forEach(function(id) {
            form_data.append('members[]', id);
        });
        form_data.append('_method', 'PUT');
        submitAjaxForm(
            '{{ url('/') }}/client/team/'+teamid+'/members',
            form_data,
            (result) => { $("#teamMembersTbl").DataTable().ajax.reload(); }
        )
    }

    tableMembers = $('#membersTbl').DataTable({
        ajax: {
            url: "{{ route('client.member.datatable') }}",
            type: 'POST',
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            data: function (d) {
                d.active = 1
            }
        },
        dom:"<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'l>>" +
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
        scrollY:true,
        scrollX:true,
        scrollCollapse: false,
        language: {
            search: "",
            searchPlaceholder: "Search Members"
        },
        columns: [
            {data:null, orderable: false, className: 'text-center', defaultContent: '<div class="form-check d-inline-block"><input class="form-check-input row_selector" type="checkbox"><label class="form-check-label"></label></div>', },
            {data: "id", visible: false, width: '100px'},
            {data: 'lastName', render: function (val, type, row) {
                    if (type === 'display') {
                        return '<div class="member-tbl-line">' +
                                    '<div class="v-avatar member-avatar">' +
                                        '<span>' + row['firstName'][0] + val[0] + '</span>' +
                                    '</div>' + row['firstName'] + ' ' + val +
                                '</div>';
                    }
                    return val;
                }
            },
        ],
        select: {
            style: 'multi',
            selector: 'td .row_selector'
        },
        order: [[2, 'asc']],
    });

    tableTeamMembers = $('#teamMembersTbl').DataTable({
        ajax: {
            url: "{{ route('client.member.datatable') }}",
            type: 'POST',
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            data: function (d) {
                d.active = 1
                d.teams = [$('#manage-team-select').val()]
            }
        },
        dom:"<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'l>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        serverSide: true,
        pageLength: 20,
        responsive: false,
        paging: false,
        lengthChange: false,
        searching: false,
        ordering: true,
        info: false,
        autoWidth: false,
        destroy: true,
        scrollY: true,
        scrollX: true,
        scrollCollapse: false,
        language: {
            search: "",
            searchPlaceholder: "Search Members"
        },
        columns: [
            {data: "id", visible: false, width: '100px'},
            {data: 'lastName', render: function (val, type, row) {
                    if (type === 'display') {
                        return '<div class="member-tbl-line">' +
                            '<div class="v-avatar member-avatar">' +
                            '<span>' + row['firstName'][0] + val[0] + '</span>' +
                            '</div><b>' + row['firstName'] + ' ' + val + '</b>' +
                            '</div>';
                    }
                    return val;
                }
            },
            {data: 'email'},
            {
                data: null,
                className: "text-center",
                defaultContent: '<div class="btn-group">'+
                    '<button type="button" class="btn btn-sm js-bs-tooltip-enabled editor_remove" data-bs-toggle="tooltip" aria-label="Delete"><i class="fa fa-fw fa-trash"></i></button>'+
                    '</div>',
                responsivePriority: 1,
                sortable: false,
                searchable: false,
                width: '100px'
            }
        ],
        order: [[1, 'asc']],
        drawCallback: function() {
            if($('#manage-team-select').val()) {
                let totalRecords = $('#teamMembersTbl').DataTable().data().count();
                if (totalRecords === 0) {
                    $('#team-members-tbl').css('display', 'none');
                    $('#no-team-members-block').css('display', 'flex');
                } else {
                    $('#team-members-tbl').css('display', 'block');
                    $('#no-team-members-block').css('display', 'none');
                }
            } else {
                $('#no-team-members-block').css('display', 'none');
            }

        },
    }).on('click', 'button.editor_remove', function (e) {// Delete a record
        e.preventDefault();

        let data = tableTeamMembers.row( $(this).closest('tr') ).data();
        let id = data["id"];
        let teamid = $('#manage-team-select').val();
        let form_data = new FormData();
        form_data.append('member', id);
        form_data.append('_method', 'DELETE');
        submitAjaxForm(
            '{{ url('/') }}/client/team/'+teamid+'/members',
            form_data,
            (result) => { $("#teamMembersTbl").DataTable().ajax.reload(); }
        )
    });

    // $('#manage-team-select').on('change', function(e){
    //     $("#teamMembersTbl").DataTable().ajax.reload();
    // });

    $(document).ready(function() {
        $('div#membersTbl_filter input').addClass('search-input');
        let search_icon = '<span onclick="openMembersSearchBar()" id="search-icon" class="search-icon"><a type="button"><i class="fa fa-search"></i></a></span>';
        $('div#membersTbl_filter').prepend(search_icon);
        $('.block-title').each(function () {
            if($(this).text() === 'Manage teams') {
                $(this).append('<span id="manage-teams-block-subtitle" style="font-weight: 300; padding-left: 6px"></span>');
            }
        });
    });

    function openMembersSearchBar() {
        $('div#membersTbl_filter').addClass('expand');
        $('div#membersTbl_filter input').addClass('search-input-open').focus();
        $('#search-icon').addClass('search-icon-open');
    }

    $(document).on('click', function () {
        if (!$('div#membersTbl_filter input').is(":focus")) {
            $('div#membersTbl_filter').removeClass('expand');
            $('div#membersTbl_filter input').removeClass('search-input-open');
            $('#search-icon').removeClass('search-icon-open');
        }
    });

    function getTeamSelectionFromLocalStorage(){
        return getStateLocal('team-selection-change');
    }

    $(document).ready( function() {
        getTeams();
    })

</script>
