@extends('layouts.app')

@section('content')
        <div class="row" id="one-on-one">
            <div class="col">
                <table class="table table-hover table-borderless table-vcenter nowrap" id="meetingsTbl" style="table-layout: fixed;">
                    <thead style="display: none">
                    <tr>
                        <th>ID</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <!--<th>{{ __('Actions') }}</th> -->
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
@endsection

@section('scripts')
    <script>
        setPageTitle('{{ __('Meetings') }}');
        addBreadcrumbItem('1-on-1\'s', '{{ route('client.1-1.index') }}');
        addBreadcrumbItem('{{ __('Meetings') }}', null);

        let tableMeetings = $('#meetingsTbl').DataTable({
            ajax: {
                url: "{{ route('client.meeting.datatable')}}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            },
            dom:"<'row'<'col-sm-12 col-md-8'l><'col-sm-12 col-md-4'f>>" +
                "<'row'<'col-sm-12'tr>>",
            serverSide: true,
            pageLength: 20,
            responsive: false,
            paging: false,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: false,
            autoWidth: true,
            destroy: true,
            scrollY: "70vh",
            // scrollX: true,
            scrollCollapse: true,
            language: {
                search: "",
                searchPlaceholder: "Search Meetings"
            },
            columns: [
                { data: 'id', visible: false, searchable: false, width: '100px' },
                { data: 'title',  searchable: true },
                { data: 'created_at', className: 'text-center', width: '180px' },
                { data: 'status', className: 'd-inline-flex justify-content-center p-0',  searchable: true, width: '150px', render: function (val, type, row) {
                        if (type === 'display') {
                            let html = '<div class="d-inline-block py-1 px-3 rounded-pill';
                            if (val==='Active') {
                                html += ' red">';
                            } else if (val==='Done') {
                                html += ' green">';
                            } else {
                                html += '">';
                            }
                            html += val + '</div>';
                            return html;
                        }
                        return val;
                    } },
                /*{
                    data: null,
                    className: "text-center",
                    render: function (val, type, row) {
                        if (type === 'display') {
                            let html = '<div class="btn-group">';
                            html+= '<button type="button" class="btn btn-primary btn-sm editor_open_meet">{{__('Meet')}} <i class="fa-solid fa-arrow-right"></i></button>';
                            html+='</div>';
                            return html;

                        }
                        return val;
                    },
                    responsivePriority: 1,
                    sortable: false,
                    searchable: false,
                    width: '130px'
                }*/
            ],
            order: [[1, 'asc']],
            initComplete: function () {
                let html = '<div class="one-on-one-heading-title">Previous 1-on-1\'s</div>';
                $(html).appendTo( $('.col-md-8:eq(0)', '#meetingsTbl_wrapper.dataTables_wrapper' ) );
            },
        }).on('click', 'button.editor_open_meet', function (e) {// Delete a record
            e.preventDefault();

            let data = tableMeetings.row( $(this).closest('tr') ).data();
            let id = data["id"];

            window.location.href = '{{ url('/').'/client/meeting/' }}'+ id +'/edit';
        });
    </script>
@endsection
