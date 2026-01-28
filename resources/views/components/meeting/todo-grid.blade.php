<div>
    <style>
        #todoTbl_wrapper div.dataTables_paginate {
            display:none;
        }

        #todoTbl_wrapper thead {
            display: none;
        }


    </style>
    <div id="todo_head" class="pb-2">
        <label class="form-label me-4"><b>{{ __('General') }}</b> <span id="todo-block-subtitle">(0)</span></label>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter nowrap" id="todoTbl">
            <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>{{ __('Descr.') }}</th>
                <th>{{ __('Date') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class='empty-table-block' id="todoTblEmpty">
            <div class='empty-table'>
                <span class='empty-table-text'>There is nothing here</span>
                <img width='21' height='21' src='{{asset('images/emoji-icons/neutral.svg')}}' alt=''>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        let editor = new $.fn.dataTable.Editor( {
            ajax: {
                create: {
                    type: 'POST',
                    url:  '{{ route('client.meeting.todos.store', $meeting) }}',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                },
                edit: {
                    type: 'PUT',
                    url:  '{{ url('/') }}/client/meeting/{{ $meeting }}/todos/{id}',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                },
                remove: {
                    type: 'DELETE',
                    url:  '{{ url('/') }}/client/meeting/{{ $meeting }}/todos/{id}',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                }
            },
            table: "#todoTbl",
            fields: [{
                label: "{{ __('Description') }}:",
                name: "note",
                type: 'textarea',
                attr: {
                    class: 'todo_input',
                    placeholder: 'Add a description',
                }
            },{
                label: "{{ __('Done') }}:",
                name: "done",
                type: 'hidden',
                //type:  "checkbox",
                //options: [
                //    { label: "Done", value: 1 }
                //],
                //separator: '',
                //unselectedValue: 0
            },{
                label: "{{ __('Date') }}:",
                name: "due_date",
                type: 'datetime',
                attr: {
                    placeholder: 'Choose date',
                }
            }]
        });

        let tableTodos = $('#todoTbl').DataTable({
            ajax: {
                url: "{{ route('client.meeting.todos.datatable', $meeting) }}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            },
            serverSide: true,
            pageLength: 10,
            responsive: false,
            paging: true,
            lengthChange: false,
            searching: false,
            ordering: true,
            info: false,
            autoWidth: true,
            scrollY: "280px",
            scrollCollapse: true,
            // scroller:{
            //     rowHeight: 100
            // },
            deferRender:true,
            language: {
                search: "",
                searchPlaceholder: "Search Todos"
            },
            columns: [
                {data: 'done', className: "text-center", width: '100px', render: function (val, type, row) {
                        if (type === 'display') {
                            return '<div class="form-check"><input class="form-check-input editor-done" type="checkbox" name="done[]"></div>';
                        }
                        return val;
                    }
                },
                {data: "id", searchable: false, visible: false, width: '100px'},
                {data: 'note', className: "editable", sortable: false, render: function (val, type, row) {
                        if (type === 'display') {
                            return "<div style='white-space:normal;' class='edit-cell'>" + val + "</div>";
                           // if (val.length >50) return val.substr( 0, 50 ) +'…'; //+ ' <i class="fa fa-pencil"/>'
                           // else return val ; //+ ' <i class="fa fa-pencil"/>'
                        }
                        return val;
                    }
                },
                {
                    data: 'due_date', className: "editable", width: '160px', render: function (val, type, row) {
                        if (type === 'display') {
                            return "<div class='edit-cell'>" + val + "</div>";
                        }
                        return val;
                    }
                },
                {
                    data: null,
                    className: "text-center",
                    defaultContent: '<div class="btn-group">'+
                        //'<button type="button" class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled editor_edit" data-bs-toggle="tooltip" aria-label="Edit"><i class="fa fa-fw fa-pencil-alt"></i></button>'+
                        '<button type="button" class="btn btn-small-outline-red js-bs-tooltip-enabled editor_remove" data-bs-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-trash"></i></button>'+
                        '</div>',
                    responsivePriority: 1,
                    sortable: false,
                    searchable: false,
                    width: '80px'
                }
            ],
            order: [[3, "asc"]],
            rowCallback: function (row, data) {
                // Set the checked state of the checkbox in the table
                $('input.editor-done', row).prop('checked', data.done == 1);
            },
            initComplete: function () {
                new $.fn.dataTable.Buttons(tableTodos, [
                    { extend: "create" , editor: editor, className: 'btn-purple btn-sm-purple', text:'<i class="fa fa-fw fa-plus"></i>', formTitle:'{{ __('Add new item') }}'
                        //action: function () { window.location.href = ''; }
                    }
                ]);

                // $( $.fn.DataTable.Editor.display.bootstrap.node() ).addClass( 'modal-header-purple' );

                //tableTodos.buttons().container().appendTo( $('.col-md-6:eq(0)', tableTodos.table().container() ) );
                tableTodos.buttons().container().appendTo( $('#todo_head' ) );

                $('.buttons-create').each(function() {
                    $(this).removeClass('btn-secondary').addClass('btn-primary btn-sm')
                });
            },
            drawCallback: function() {
                let totalRecords = $('#todoTbl').DataTable().data().count();
                $('#todo-block-subtitle').text('('+totalRecords+')')
                if (totalRecords > 0) {
                    $('#todoTbl>tbody').css('display', 'table-row-group');
                    $("#todoTblEmpty").css('display', 'none');
                } else {
                    $('#todoTbl>tbody').css('display', 'none');
                    $("#todoTblEmpty").css('display', 'flex');
                }
            },
        }).on('change', 'input.editor-done', function () {

            let done = $(this).prop('checked') ? 1 : 0;
            let row = tableTodos.row( $(this).closest('tr') );
            editor
                .edit( row, false )
                .set( 'done', done )
                .submit();

        }).on('click', 'button.editor_edit', function (e) {// Edit record
            e.preventDefault();

            editor.edit( $(this).closest('tr'), {
                title: '{{ __('Edit item') }}',
                buttons: '{{ __('Update') }}',
                className: 'modal-header-purple'
            });

        }).on('click', 'button.editor_remove', function (e) {// Delete a record
            e.preventDefault();

            editor.remove( $(this).closest('tr'), {
                title: "{{ __('Delete Item') }}",
                message: "<p>{{ __('Are you sure you wish to remove this item?') }}</p>",
                buttons: "{{ __('Delete') }}",
                className: "modal-header-purple modal-hide-footer"
            });
        }).on( 'click', 'tbody .editable', function (e) {
            e.stopImmediatePropagation(); // stop the row selection when clicking
            let data = tableTodos.row( $(this).closest('tr') ).data();

            if (data['done']==0)
                editor.inline( this, {
                    //onBlur: 'submit'
                    buttons: { label: '&gt;', fn: function () { this.submit(); } }
                });
        });


    </script>
@endpush
