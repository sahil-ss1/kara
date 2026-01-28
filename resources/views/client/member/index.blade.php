@extends('layouts.app')

@section('content')
    <x-block title="Members" >
        <table class="table table-bordered table-vcenter" id="membersTbl">
            <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </x-block>
@endsection

@section('scripts')
    <script>
        setPageTitle('{{ __('Members') }}');
        addBreadcrumbItem('{{ __('Members') }}', null);

        let editor = new $.fn.dataTable.Editor( {
            ajax: {
                edit: {
                    type: 'PUT',
                    url:  '{{ url('/') }}/client/member/{id}',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                },
            },
            table: "#membersTbl",
            fields: [{
                label: "{{ __('Active') }}:",
                name: "active",
                type: "select",
                def: "1",
                options: [
                    { label: "Yes", value: "1" },
                    { label: "No", value: "0" }
                ]
            }]
        });

        let tableMembers = $('#membersTbl').DataTable({
            ajax: {
                url: "{{ route('client.member.datatable') }}",
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
            searching: false,
            ordering: true,
            info: false,
            autoWidth: false,
            language: {
                search: "",
                searchPlaceholder: "Search Members"
            },
            columns: [
                {data: "id", visible: false, width: '100px'},
                {data: 'lastName', render: function (val, type, row) {
                        if (type === 'display') {
                            return row['firstName']+' '+val;
                        }
                        return val;
                    }
                },
                {data: 'email'},
                {data: 'active', className: "text-center", width: '120px', render: function (val, type, row) {
                        if (type === 'display') {
                            let archived = (row['hubspot_archived']==1)?'<em style="font-size:smaller">archived</em>':'';
                            return '<div class="form-check form-switch"><input class="form-check-input editor-active" type="checkbox" name="active[]">'+archived+'</div>';
                        }
                        return val;
                    }
                }
            ],
            order: [[3, 'desc'], [1, 'asc']],
            rowCallback: function (row, data) {
                // Set the checked state of the checkbox in the table
                $('input.editor-active', row).prop('checked', data.active == 1);
            },
            initComplete: function () {
                new $.fn.dataTable.Buttons(tableMembers, [
                    { text:'<i class="fa fa-fw fa-plus"></i> {{ __('Sync') }}', className: 'el-button el-button--info',
                      action: function () {
                          $.get( "{{ route('client.member.sync') }}", function( data ) {
                              alert( "Load was performed." );
                              $("#membersTbl").DataTable().ajax.reload()
                          });
                      }
                    }
                    //{ extend: "edit",   editor: editor },
                    //{ extend: "remove", editor: editor }

                    //{ extend: "edit",   editor: editor },
                    //{ extend: "remove", editor: editor }
                ]);

                tableMembers.buttons().container().appendTo( $('.col-md-6:eq(0)', tableMembers.table().container() ) );

                $('.buttons-create').each(function() {
                    $(this).removeClass('btn-secondary').addClass('btn-primary')
                });

            }
        }).on('change', 'input.editor-active', function () {
            let active = $(this).prop('checked') ? 1 : 0;
            let row = tableMembers.row( $(this).closest('tr') );
            editor
                .edit( row, false )
                .set( 'active', active )
                .submit();
        });

    </script>
@endsection
