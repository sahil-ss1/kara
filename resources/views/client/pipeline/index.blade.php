@extends('layouts.app')

@section('content')
    <x-block title="My Pipelines" >
        <table class="table table-bordered table-vcenter" id="pipelinesTbl">
            <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </x-block>
@endsection

@section('scripts')
    <script>
        setPageTitle('{{ __('Pipelines') }}');
        addBreadcrumbItem('{{ __('Pipelines') }}', null);

        let editor = new $.fn.dataTable.Editor( {
            ajax: {
                edit: {
                    type: 'PUT',
                    url:  '{{ url('/') }}/client/pipeline/{id}',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                },
            },
            table: "#pipelinesTbl",
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

        let tablePipelines = $('#pipelinesTbl').DataTable({
            ajax: {
                url: "{{ route('client.pipeline.datatable') }}",
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
                searchPlaceholder: "Search Pipeline"
            },
            columns: [
                {data: "id", visible: false, width: '100px'},
                {data: 'label'},
                {
                    data: 'active', className: "text-center", width: '120px', render: function (val, type, row) {
                        if (type === 'display') {
                            return '<div class="form-check form-switch"><input class="form-check-input editor-active" type="checkbox" name="active[]"></div>';
                        }
                        return val;
                    }
                }
            ],
            order: [[1, "desc"]],
            rowCallback: function (row, data) {
                // Set the checked state of the checkbox in the table
                $('input.editor-active', row).prop('checked', data.active == 1);
            },
            initComplete: function () {
                new $.fn.dataTable.Buttons(tablePipelines, [
                    { text:'<i class="fa fa-fw fa-plus"></i> {{ __('Sync') }}', className: 'el-button el-button--info',
                      action: function () {
                          $.get( "{{ route('client.pipeline.sync') }}", function( data ) {
                              alert( "Load was performed." );
                              $("#pipelinesTbl").DataTable().ajax.reload()
                          });
                      }
                    }
                    //{ extend: "edit",   editor: editor },
                    //{ extend: "remove", editor: editor }

                    //{ extend: "edit",   editor: editor },
                    //{ extend: "remove", editor: editor }
                ]);

                tablePipelines.buttons().container().appendTo( $('.col-md-6:eq(0)', tablePipelines.table().container() ) );

                $('.buttons-create').each(function() {
                    $(this).removeClass('btn-secondary').addClass('btn-primary')
                });

            }
        }).on('change', 'input.editor-active', function () {
            let active = $(this).prop('checked') ? 1 : 0;
            let row = tablePipelines.row( $(this).closest('tr') );
            editor
                .edit( row, false )
                .set( 'active', active )
                .submit();
        });

    </script>
@endsection
