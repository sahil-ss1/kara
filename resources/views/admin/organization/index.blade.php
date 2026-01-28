@extends('layouts.app')

@section('content')
    <x-block title="Organizations" subtitle="List">
        <table class="table table-bordered table-striped table-vcenter" id="organizationsTbl">
            <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Users') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </x-block>
@endsection

@section('scripts')
    <script>
        setPageTitle('{{ __('Organizations') }}');
        addBreadcrumbItem('{{ __('Organizations') }}', null);

        let editor = new $.fn.dataTable.Editor( {
            ajax: {
                create: {
                    type: 'POST',
                    url:  '{{ route('admin.organization.store') }}',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                },
                remove: {
                    type: 'DELETE',
                    url:  '{{ url('/') }}/admin/organization/{id}',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                }
            },
            table: "#organizationsTbl",
            fields: [{
                label: "{{ __('Name') }}:",
                name: "name",
                className: 'align-center'
            }]
        } );


        let tableOrganizations = $('#organizationsTbl').DataTable({
            ajax: {
                url: "{{ route('admin.organization.datatable') }}",
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
                searchPlaceholder: "Search Organizations"
            },
            columns: [
                {data: "id", searchable: false, visible: true, width: '100px'},
                {data: 'name'},
                {data: 'users_count', width:'100px'},
                {
                    data: null,
                    className: "text-center",
                    defaultContent: '<div class="btn-group">'+
                        '<button type="button" class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled editor_remove" data-bs-toggle="tooltip" aria-label="Delete"><i class="fa fa-fw fa-times"></i></button>'+
                        '</div>',
                    responsivePriority: 1,
                    sortable: false,
                    searchable: false,
                    width: '100px'
                }
            ],
            order: [[1, "desc"]],
            initComplete: function () {
                new $.fn.dataTable.Buttons(tableOrganizations, [
                    { extend: "create" , editor: editor, text:'<i class="fa fa-fw fa-plus"></i> {{ __('Organization') }}', formTitle:'{{ __('Add new organization') }}', className: 'el-button el-button--info' }
                ]);

                tableOrganizations.buttons().container().appendTo( $('.col-md-6:eq(0)', tableOrganizations.table().container() ) );

                $('.buttons-create').each(function() {
                    $(this).removeClass('btn-secondary').addClass('btn-primary')
                });

            }
        }).on('click', 'button.editor_remove', function (e) {// Delete a record
            e.preventDefault();

            editor.remove( $(this).closest('tr'), {
                title: "{{ __('Delete organization') }}",
                message: "<p>{{ __('Are you sure you wish to remove this organization?') }}</p>",
                buttons: "{{ __('Delete') }}"
            });

        });

    </script>
@endsection

