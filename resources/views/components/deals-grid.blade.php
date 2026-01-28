<div style="cursor: default">
    <div class="modal" id="{{ $table_id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $table_id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-header-purple modal-hide-footer" role="document">
            <div class="modal-content">
                <div class="block block-rounded block-transparent mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"><span class="name"></span> <span id="{{ $table_id }}-block-subtitle" style="font-weight: 300; padding-left: 6px"></span></h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm">
                        <div class="table-responsive">
                            <table class="table table-vcenter nowrap" id="{{ $table_id }}-table" style="table-layout: fixed; font-size: 0.9em">
                                <thead style="font-size: 0.8em;">
                                <tr>
                                    <th>ID</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Deal owner') }}</th>
                                    <th>{{ __('Creation Date') }}</th>
                                    <th>{{ __('Close Date') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Kara %') }}</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div class='empty-table-block' id="{{ $table_id }}-tableEmpty">
                                <div class='empty-table mt-1'>
                                    <span class='empty-table-text'>There is nothing here</span>
                                    <img width='21' height='21' src='{{asset('images/emoji-icons/neutral.svg')}}' alt=''>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block-content block-content-full text-end bg-body">
                        <button type="button" class="btn btn-sm btn-alt-secondary me-1" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Perfect</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('components_scripts')
<script>
    function showDealsGrid(name, filters){
        //console.log(filters);
        $('#{{ $table_id }} .name').text(name);
        initDealsGridDatatable(filters);
        let myModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('{{ $table_id }}'), {
            backdrop: true
        })
        myModal.show();
    }

    function initDealsGridDatatable(data){
        let editor = new $.fn.dataTable.Editor( {
            ajax: {
                edit: {
                    type: 'PUT',
                    url:  '{{ url('/') }}/client/deal/{id}',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                },
                //remove: {
                //    type: 'DELETE',
                //    url:  '{{ url('/') }}/admin/user/{id}',
                //    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                //}
            },
            table: "#{{ $table_id }}-table",
            fields: [{
                label: "{{ __('Amount') }}:",
                name: "amount"
            },{
                label: "{{ __('Status') }}:",
                name: "stage_id",
                type: 'select'
            },{
                label: "{{ __('Close Date') }}:",
                name: "closedate",
                type: 'datetime'
            },{
                label: "{{ __('Kara %') }}:",
                name: "kara_probability",
                type: "text",
                attr:  {
                    type: 'number',
                }
            }]
        });

        editor.on( 'initEdit', function ( e, node, data, items, type ) {
            let id = data['id']
            $.ajax ({
                url: '{{ url('/') }}/client/deal/'+id+'/stages',
                dataType: 'json',
                success: function (json) {
                    editor.field('stage_id').update( json.options.stages );
                }
            })
        })

        let tableDeals = $('#{{ $table_id }}-table').DataTable({
            ajax: {
                url: "{{ route('client.deal.datatable') }}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                data: data
            },
            dom:"<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'>>",
            serverSide: true,
            processing: true,
            pageLength: 50,
            responsive: false,
            paging: true,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: true,
            destroy: true,
            scrollY: "64vh",
            scrollCollapse: true,
            scroller:true,
            deferRender:true,
            // deferLoading: 0, //loa empty until first refresh
            language: {
                search: "",
                searchPlaceholder: "Search Deals"
            },
            columns: [
                {data: "id", visible: false, width: '100px'},
                {data: 'name', className: 'deal-eye-cell', render: function (data, type, row) {
                        if (type === 'display') {
                            if (row.name.length > 16) return '<i class="fa-solid fa-eye editor_edit btn-eye" id="deal_details" style="cursor:pointer"></i>'+'<span data-toggle="tooltip" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="'+row.name+'">' + row.name.substr( 0, 14 ) +'…' + '</span>'
                            else return '<i class="fa-solid fa-eye editor_edit btn-eye" id="deal_details" style="cursor:pointer"></i>'+row.name
                        }
                        return data;
                    }
                },
                {
                    data: 'owner_name',
                    className: 'deal-grid-owner-cell',
                    render: function (val, type, row) {
                        if (type === 'display') {
                            return '<a href="{{ url('/') }}/client/profile/'+row["owner_id"]+'">' +
                                '<div class="v-avatar member-avatar my-2" style="height: 34px; width: 34px; border: 1px solid #fff!important;" data-toggle="tooltip" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="'+row["owner_firstName"]+' '+row["owner_lastName"]+'">' +
                                '<span>' + row["owner_firstName"][0] + row["owner_lastName"][0] + '</span>' +
                                '</div></a>';
                        }
                        return val;
                    }
                },
                {data: 'createdate'},
                {data: 'closedate', className: "editable", render: function ( data, type, row ) {
                        if ( type === 'display' ) {
                            return '<div class="edit-cell">'+data+'</div>';
                        }
                        return data;
                    }
                },
                {data: 'amount', className: "editable", render: function ( data, type, row ) {
                        if ( type === 'display' ) {
                            let numberRenderer = $.fn.dataTable.render.number( ',', '.', 0, '{!! $currency !!}' ).display;
                            return '<div class="edit-cell">'+numberRenderer( data )+'&nbsp;</div>';
                        }
                        return data;
                    }
                },
                {data: 'stage_id', className: "editable", render: function (data, type, row) {
                        if (type === 'display') {
                            //let ellipsisRenderer = $.fn.dataTable.render.ellipsis(10);
                            //return ellipsisRenderer(row.stage.label) + ' <i class="fa fa-pencil"/>';
                            if (row.stage.label.length > 20) return '<div class="edit-cell" data-toggle="tooltip" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="'+row.stage.label+'">'+row.stage.label.substr( 0, 20 ) +'…' + '</div>'
                            else return '<div class="edit-cell">'+row.stage.label+'</div>'
                        }
                        return data;
                    }
                },
                {data: 'kara_probability', className: "editable", render: function (val, type, row) {
                        if ((type === 'display')) {
                            if (val) return '<div class="edit-cell">'+(val*100)+"%"+'</div>'
                            else return '<div class="edit-cell">&nbsp;</div>'
                        }
                        return val;
                    }
                },
            ],
            order: [[1, "desc"]],
            initComplete: function () {
                $('#{{ $table_id }}-table').DataTable().ajax.reload();
            },
            drawCallback: function() {
                let totalRecords = $('#{{ $table_id }}-table').DataTable().page.info().recordsTotal;
                $('#{{ $table_id }}-block-subtitle').text('('+totalRecords+')');
                if (totalRecords > 0) {
                    $('#{{ $table_id }}-table>tbody').css('display', 'table-row-group');
                    $('#{{ $table_id }}-tableEmpty').css('display', 'none');
                    $('#{{ $table_id }}-table_wrapper>.row>.col-sm-12>.dataTables_scroll>.dataTables_scrollBody').css('max-height', '64vh');
                    $('#{{ $table_id }}-table_info').css('display', 'block');
                } else {
                    $('#{{ $table_id }}-table>tbody').css('display', 'none');
                    $('#{{ $table_id }}-tableEmpty').css('display', 'flex');
                    $('#{{ $table_id }}-table_wrapper>.row>.col-sm-12>.dataTables_scroll>.dataTables_scrollBody').css('max-height', '0');
                    $('#{{ $table_id }}-table_info').css('display', 'none');
                }
            },
        }).on('click', '.editor_edit', function (e) {// Edit record
            e.preventDefault();

            let data = $('#{{ $table_id }}-table').DataTable().row( $(this).closest('tr') ).data();
            let id = data["DT_RowId"];

            let extra_class = data['probability'] === '100%' ? ' modal-header-green' : '';
            extra_class += data['probability'] === '0%' ? ' modal-header-red' : '';

            createModalOverModal(
                'Deal details',
                null,
                '{{ url('/') }}/client/deal/'+id+'/edit',
                'modal-lg deal-edit-modal-content' + extra_class ,
                function(event){ return onDialogOpen2(event, id) },
                function(modal) {
                    let form_data = new FormData($('#form-deal-update')[0]);
                    let action = $('#form-deal-update').attr('action');
                    submitAjaxForm(
                        action,
                        form_data,
                        function(e) {
                            modal.hide();
                            document.getElementById('deals-overview').dispatchEvent(new CustomEvent('refresh-teams', { detail: {}}));
                            $('#{{ $table_id }}-table').DataTable().ajax.reload();
                        }
                    )
                },
                function(e){
                    document.getElementById('deals-overview').dispatchEvent(new CustomEvent('refresh-teams', { detail: {}}));
                    $('#{{ $table_id }}-table').DataTable().ajax.reload();
                },
                '1062',
                '5.4rem 0 0 0',
                null,
                null
            );
        }).on( 'click', 'tbody .editable', function (e) {
            e.stopImmediatePropagation(); // stop the row selection when clicking
            editor.inline( this, {
                //onBlur: 'submit'
                buttons: { label: '&gt;', fn: function () { this.submit(); } }
            });
        });

        document.getElementById('{{ $table_id }}').addEventListener('hide.bs.modal', function (event) {
            Livewire.dispatchTo(null,'dashboard.new-deals-widget', 'refresh-counter');
            Livewire.dispatchTo(null,'dashboard.deals-in-progress-widget', 'refresh-counter');
            Livewire.dispatchTo(null,'dashboard.deals-lost-widget', 'refresh-counter');
            Livewire.dispatchTo(null,'dashboard.deals-won-widget', 'refresh-counter');
            Livewire.dispatchTo(null,'dashboard.average-basket-widget', 'refresh-counter');
            Livewire.dispatchTo(null,'dashboard.total-tasks-widget', 'refresh-counter');
            Livewire.dispatchTo(null,'dashboard.total-calls-widget', 'refresh-counter');
            Livewire.dispatchTo(null,'goals.goal-dashboard', 'refresh-dashboard');
            $('#dealsWidgetTbl').DataTable().ajax.reload();
            $('#{{ $table_id }}-block-subtitle').text('');
            $("#{{ $table_id }}").off();
            tableDeals=null;
        });
    }

    function onDialogOpen2(event, id){
        let idx = $('#{{ $table_id }}-table').DataTable().row('#'+id).index();
        let lastIdx = $('#{{ $table_id }}-table').DataTable().data().count() - 1;
        let $modalArrows = $('.modal.show .deal-edit-modal-content .modal-content');
        let html = '';
        if (idx>0) html += '<button type="button" class="btn btn-alt-secondary arrow-button arrow-left" title="previous deal" data-next="'+(idx-1)+'" onclick="moveto2(this)"><i class="fa-solid fa-arrow-left"></i></button>';
        if (idx!==lastIdx) html += '<button type="button" class="btn btn-alt-secondary arrow-button arrow-right" title="next deal" data-next="'+(idx+1)+'" onclick="moveto2(this)"><i class="fa-solid fa-arrow-right"></i></button>';

        $modalArrows.prepend(html);
    }

    function moveto2(el){
        let idx = $(el).data('next');
        let form_data = new FormData($('#form-deal-update')[0]);
        let action = $('#form-deal-update').attr('action');
        submitAjaxForm(
            action,
            form_data,
            function(e) {
                let row = $('#{{ $table_id }}-table').DataTable().row(idx);
                if (row) $(row.node()).find('.editor_edit').click();
            }
        )
    }
</script>
@endpush
