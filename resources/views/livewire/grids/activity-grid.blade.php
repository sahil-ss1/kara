<div>
    <label class="mt-3 ms-3" style="text-transform: uppercase"><b>Deal's activities</b><span id="activitiesTotal" class="mx-1"></span></label>
    <button type="button" class="btn-purple btn-sm-purple" tabindex="0" onclick="create_task()"><i class="fa fa-fw fa-plus"></i></button>
    <div class="table-responsive">
        <table class="table table-vcenter nowrap" id="activitiesTbl" style="table-layout: fixed;">
            <thead style="display: none">
            <tr>
                <th>ID</th>
                <th>{{ __('Activity Name') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Owner') }}</th>
                <th>{{ __('Creation Date') }}</th>
                <th>{{ __('Hubspot Task Type') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
    tableActivity = $('#activitiesTbl').DataTable({
        ajax: {
            url: "{{ url('/').'/client/deal/'. $deal->id.'/activity/datatable' }}",
            type: 'POST',
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
        },
        dom:"<'row py-1'<'col-sm-12 col-md-8'l><'col-sm-12 col-md-4'>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'>>",
        serverSide: false,
        pageLength: 50,
        responsive: false,
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: true,
        destroy: true,
        scrollY:400,
        scrollCollapse: true,
        scroller:true,
        deferRender:true,
        //deferLoading: 0, //loa empty until first refresh
        language: {
            search: "",
            searchPlaceholder: "Search",
            emptyTable: "This deal doesn't have Activity. You can add one by clicking the plus button."
        },
        columns: [
            {data: "id", visible: false, width: '100px'},
            {data: 'hubspot_task_subject', render: function (data, type, row) {
                    if (data && type === 'display') {
                        if (data.length > 15) return '<div data-toggle="tooltip" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="'+data+'">'+data.substr( 0, 15 ) +'…' + '</div>'
                        else return data
                    }
                    return data;
                }
            },
            {data: 'type' },
            {data: 'owner', render: function (data, type, row) {
                    if (data && type === 'display') {
                        if (data.length > 16) return '<div data-toggle="tooltip" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="'+data+'">'+data.substr( 0, 16 ) +'…' + '</div>'
                        else return data
                    }
                    return data;
                }
            },
            {data: 'hubspot_createdAt', className: "text-center"},
            // {data: 'hubspot_task_type', width: '50px'},
            {
                data: null,
                className: "text-center",
                render: function (val, type, row) {
                    let html = '';
                    @foreach(\App\Enum\TaskType::cases() as $task_type)
                        // foreach task display icon
                        var task = {!! json_encode($task_type) !!};
                        if(row['hubspot_task_type'] === task) {
                            html = '<span><img src="{{asset($task_type->icon())}}" class="me-2" width="24" height="24" alt=""></span>';
                        }
                    @endforeach
                    return html;
                },
                responsivePriority: 1,
                sortable: false,
                searchable: false,
                width: '100px'
            },
            {
                data: null,
                className: "text-center",
                render: function (val, type, row) {
                    let html = '';
                    if (row['type']==='Task') html += '<button type="button" class="btn btn-outline-secondary btn-round" id="dropdown-default-outline-secondary" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis more-icon"></i></button>'+
                        '<div class="dropdown-menu dropdown-menu-end goal-dropdown fs-sm" aria-labelledby="dropdown-default-outline-secondary">'+
                        '<a class="dropdown-item goal-dropdown-item editor_task_edit"><i class="fa fa-pencil goal-dropdown-icon"></i>Edit</a>'+
                        '<a class="dropdown-item goal-dropdown-item editor_task_remove"><i class="fa fa-trash goal-dropdown-icon"></i>Delete</a>'+
                        '</div>';
                    return html;
                },
                responsivePriority: 1,
                sortable: false,
                searchable: false,
                width: '100px'
            }

        ],
        order: [[3, "desc"]],
        drawCallback: function() {
            let totalRecords = $('#activitiesTbl').DataTable().data().count();
            $('#activitiesTotal').text('('+totalRecords+')');
            if (totalRecords > 1) {
                $('.dataTables_scrollBody').css('min-height', '186px');
            } else if (totalRecords > 0) {
                $('.dataTables_scrollBody').css('min-height', '136px');
            } else {
                $('.dataTables_scrollBody').css('min-height', 'unset');
            }
        },
    }).on('click', 'a.editor_task_edit', function (e) {// Edit record
        e.preventDefault();

        //let taskid = $(this).data('id');
        let data = tableActivity.row( $(this).closest('tr') ).data();
        let taskid = data["DT_RowId"];//.substring(4);//data["id"];

        createModalOverModal(
            '{{ __('Edit task') }}',
            null,
            '{{ url('/') }}/client/task/'+taskid+'/edit',
            'modal-md modal-height-60',
            null,
            function(modal) {
                submitAjaxFormWithValidation(
                    $('#form-task-update'),
                    function(e) {
                        modal.hide();
                        tableActivity.ajax.reload();
                    }
                )
            },
            null,
            '1072',
            '5.4rem 0 0 0',
            null,
            null
        )

    }).on('click', 'a.editor_task_remove', function (e) {// Delete a record
        //let taskid = $(this).data('id');
        let data = tableActivity.row( $(this).closest('tr') ).data();
        let taskid = data["DT_RowId"];//.substring(4);//data["id"];

        createModalOverModal(
            '{{ __('Remove task') }}',
            '{{ __('Are you sure?') }}',
            null,
            'modal-md',
            null,
            function(modal) {
                let form_data = new FormData();
                form_data.append('_method', 'DELETE');
                let action = '{{ url('/') }}/client/task/'+taskid
                submitAjaxForm(
                    action,
                    form_data,
                    function(e) {
                        modal.hide();
                        Livewire.dispatch('refreshTasks');
                        tableActivity.ajax.reload();
                    }
                )
            },
            null,
            '1072',
            '5.4rem 0 0 0',
            null,
            null
        )

    });

    function create_task(){
        // let owner = $(el).data('owner');
        let url = '{{ url('/') }}/client/task/create?deal={{$deal->id}}';
        {{--let url = '{{ url('/') }}/client/task/create?owner='+owner;--}}
        // let dealid = $(el).data('deal');
        // if (dealid) url += '&deal='+dealid;

        createModalOverModal(
            '{{__('New task') }}',
            null,
            url,
            'modal-md modal-height-60',
            null,
            function(modal) {
                submitAjaxFormWithValidation(
                    $('#form-task-create'),
                    function(e) {
                        modal.hide();
                        Livewire.dispatch('refreshTasks');
                        tableActivity.ajax.reload();
                    }
                )

                //let form_data = new FormData($('#form-task-create')[0]);
                //let action = $('#form-task-create').attr('action');
                //submitAjaxForm(
                //    action,
                //    form_data,
                //    function(e) {
                //        modal.hide();
                //        Livewire.dispatch('refreshTasks');
                //    }
                //)
            },
            null,
            '1072',
            '5.4rem 0 0 0',
            null,
            null
        )
    }
</script>
