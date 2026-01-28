@extends('layouts.app')

@section('content')
    <x-block title="Users" subtitle="List">
        <table class="table table-bordered table-striped table-vcenter" id="usersTbl">
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Organization') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Active') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </x-block>
@endsection

@section('scripts')
<script>
    setPageTitle('{{ __('Users') }}');
    addBreadcrumbItem('{{ __('Users') }}', null);

    let editor = new $.fn.dataTable.Editor( {
        ajax: {
            create: {
                type: 'POST',
                url:  '{{ route('admin.user.store') }}',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            },
            edit: {
                type: 'PUT',
                url:  '{{ url('/') }}/admin/user/{id}',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            },
            remove: {
                type: 'DELETE',
                url:  '{{ url('/') }}/admin/user/{id}',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            }
        },
        table: "#usersTbl",
        fields: [{
            label: "{{ __('Name') }}:",
            name: "name",
            className: 'align-center'
        }, {
            label: "{{ __('Email') }}:",
            name: "email",
            attr: {
                type:'email'
            },
            className: 'align-center'
        }, {
            label: "{{ __('Password') }}:",
            name: "password",
            type: "password",
            className: 'align-center'
        }, {
            label: "{{ __('Confirm password') }}:",
            name: "confirm-password",
            type: "password",
            className: 'align-center'
        }, {
            label: "{{ __('Active') }}:",
            name: "active",
            type: "select",
            def: "1",
            options: [
                { label: "Yes", value: "1" },
                { label: "No", value: "0" }
            ],
            className: 'align-center'
        }, {
            label: "{{ __('Role') }}:",
            name: "role_id",
            type: "select",
            def: "2",
            options: {!! $roles !!},
            className: 'align-center'
        }]
    } );


    let tableUsers = $('#usersTbl').DataTable({
        ajax: {
            url: "{{ route('admin.user.datatable') }}",
            type: 'POST',
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            //data: function (d) {
                //d.listAllCustomers = 1
            //}
        },
        serverSide: true,
        pageLength: 20,
        responsive: false,
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        language: {
            search: "",
            searchPlaceholder: "Search Users"
        },
        columns: [
            {data:'avatar', searchable: false, sortable: false, className: "text-center", width: '90px', render: function(val, type, row){
                      return '<img class="img-avatar img-avatar48" src="'+val+'" alt="">'
                }
            },
            {data: "id", searchable: false, visible: true, width: '100px'},
            {data: 'name'},
            {data: 'email'},
            {data: 'organization_name'},
            {data: 'role_name', searchable: false, width: '100px', render: function (val, type, row) {
                    if (val === 'admin')
                        return '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">'+val+'</span>'
                    else
                        return '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-info-light text-info">'+val+'</span>'
                }
            },
            {
                data: 'active', className: "text-center", width: '120px', render: function (val, type, row) {
                    if (type === 'display') {
                        return '<div class="form-check form-switch"><input class="form-check-input editor-active" type="checkbox" name="active[]"></div>';
                    }
                    return val;
                }
            },
            {
                data: null,
                className: "text-center",
                // render: function (val, type, row) {
                //   return '<div><a class="editor_edit action" href="{{ url('/').'/user/' }}'+row.id+'/edit"><i class="material-icons">mode_edit</i></a>' +
                //       '&nbsp;<a class="editor_remove action" href=""><i class="material-icons">delete_forever</i></a>' +
                //       '&nbsp;<a class="editor_task action" href=""><i class="material-icons">task_alt</i></a></div>'
                // },
                defaultContent: '<div class="btn-group">'+
                    '<button type="button" class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled editor_edit" data-bs-toggle="tooltip" aria-label="Edit"><i class="fa fa-fw fa-pencil-alt"></i></button>'+
                    '<button type="button" class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled editor_remove" data-bs-toggle="tooltip" aria-label="Delete"><i class="fa fa-fw fa-times"></i></button>'+
                    @if ( config('app.notifications') )
                    '<button type="button" class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled editor_notify" data-bs-toggle="tooltip" aria-label="Notify"><i class="fa fa-fw fa-bell"></i></button>'+
                    @endif
                    @if( !Auth::user()->isImpersonating() )
                    '<button type="button" class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled editor_impersonate" data-bs-toggle="tooltip" aria-label="Impersonate"><i class="fa fa-fw fa-user-secret"></i></button>'+
                    @endif
                    '</div>',
                responsivePriority: 1,
                sortable: false,
                searchable: false,
                width: '100px'
            }
        ],
        order: [[1, "desc"]],
        rowCallback: function (row, data) {
            // Set the checked state of the checkbox in the table
            $('input.editor-active', row).prop('checked', data.active == 1);
        },
        initComplete: function () {
            new $.fn.dataTable.Buttons(tableUsers, [
                { extend: "create" , editor: editor, text:'<i class="fa fa-fw fa-plus"></i> {{ __('User') }}', formTitle:'{{ __('Add new user') }}', className: 'el-button el-button--info'
                  //action: function () { window.location.href = '{{ route('admin.user.create') }}'; }
                }
                //{ extend: "edit",   editor: editor },
                //{ extend: "remove", editor: editor }

                //{ extend: "edit",   editor: editor },
                //{ extend: "remove", editor: editor }
            ]);

            tableUsers.buttons().container().appendTo( $('.col-md-6:eq(0)', tableUsers.table().container() ) );

            $('.buttons-create').each(function() {
                $(this).removeClass('btn-secondary').addClass('btn-primary')
            });

        }
    }).on('change', 'input.editor-active', function () {
        let active = $(this).prop('checked') ? 1 : 0;
        let row = tableUsers.row( $(this).closest('tr') );
        editor
            .edit( row, false )
            .set( 'active', active )
            .submit();

    }).on('click', 'button.editor_edit', function (e) {// Edit record
        /*
        let data = tableUsers.row( $(this).closest('tr') ).data();
        let id = data["DT_RowId"];//.substring(4);//data["id"]; //
        window.location.href = '{{ url('/').'/admin/user/' }}'+id+'/edit'
        */

        e.preventDefault();

        editor.edit( $(this).closest('tr'), {
            title: '{{ __('Edit user') }}',
            buttons: '{{ __('Update') }}'
        });

    }).on('click', 'button.editor_remove', function (e) {// Delete a record
        e.preventDefault();

        editor.remove( $(this).closest('tr'), {
            title: "{{ __('Delete user') }}",
            message: "<p>{{ __('Are you sure you wish to remove this user?') }}</p>",
            buttons: "{{ __('Delete') }}"
        });
        /*
        let data = tableUsers.row( $(this).closest('tr') ).data();
        let id = data["DT_RowId"];//.substring(4);//data["id"]; //
        let url = '{{ url('/').'/customer/user/' }}' + id;
        let message = "Remove user and all of his/her stories?";

        let form_data = new FormData();
        form_data.append('id', id);
        form_data.append('_method', 'DELETE');
        createModal(
            'Delete user',
            message,
            null,
            null,
            (event) => submitAjaxForm(
                url,
                form_data,
                (result) => bootstrap.Modal.getInstance( document.getElementById('modal') ).hide()
            ),
            (event) => $("#usersTbl").DataTable().ajax.reload()
        );*/
    }).on('click', 'button.editor_notify', function (e) {// Delete a record
        e.preventDefault();
        let data = tableUsers.row( $(this).closest('tr') ).data();
        let id = data["DT_RowId"];
        let url = '{{ url('/').'/admin/user/notify/' }}' + id + '/create';

        createModal(
            '{{ __('Notify') }}',
            null,
            url,
            null,
            null,
            function(modal) {
                let form_data = new FormData($('#modal_notify_form')[0]);
                let action = $('#modal_notify_form').attr('action');
                submitAjaxForm(
                    action,
                    form_data,
                    (e) => modal.hide()
                )
            }
        )

    }).on('click', 'button.editor_impersonate', function (e) {// Delete a record
        e.preventDefault();
        let data = tableUsers.row( $(this).closest('tr') ).data();
        let id = data["DT_RowId"];
        let url = '{{ url('/').'/admin/user/' }}' + id + '/impersonate';

        window.location.href = url;
    });

</script>
@endsection
