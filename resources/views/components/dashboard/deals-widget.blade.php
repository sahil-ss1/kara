<style>
    #dealsWidgetTbl_wrapper .pagination, .dts_label {
        display: none;
    }
</style>
<div class="table-responsive">
    <table class="table table-vcenter nowrap" id="dealsWidgetTbl">
        <thead>
        <tr>
            <th>ID</th>
            <th>{{ __('Deal') }}</th>
            <th>{{ __('Warns') }}</th>
            <th>{{ __('Amount') }}</th>
            <th>{{ __('Deal owner') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('WIN %') }}</th>
            <th>{{ __('Close Date') }}</th>
            <th>{{ __('Last activity') }}</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div class='empty-table-block' id="dealsWidgetTblEmpty">
        <div class='empty-table deals-table-block-col my-2'>
            <span class='empty-table-text'>There is nothing here</span>
            <img width='21' height='21' src='{{asset('images/emoji-icons/neutral.svg')}}' alt=''>
        </div>
    </div>
</div>
<div class="show-more-wrap mt-2" id="show-more-deals" style="display: none">
    <div onclick="showMoreDeals()"><a type="button" class="show-more" id="deal-more"><i class="fa fa-angle-down pe-2" id="deal-more-icon"></i><span id="deal-more-text">Show more</span></a></div>
</div>

@push('scripts')
<script>
    function createDealsSelects(name, data, datatable, placeholder){
        let arr = data;

        let div = $('<div class="default-select">')
            .attr('style', 'display:inline-block;margin-right:5px;width:260px;vertical-align:bottom;');
        let sel = $('<select>')
            .attr('id',name)
            .attr('class', 'form-select')
            .on('change', function(e){
                saveStateLocal(name, $(this).val());
                $("#"+datatable).DataTable().ajax.reload();
            });
        let selected_val = getStateLocal(name);
        sel.append($("<option>").attr('value','').text(placeholder));
        $.each(arr, function(key, value) {
            if ((selected_val)&&(selected_val === key ))
                sel.append($("<option>").attr('value',key).attr('selected', 'selected').text(value));
            else
                sel.append($("<option>").attr('value',key).text(value));
        });

        div.append(sel);
        return div;
    }

    let tableDealsWidget = $('#dealsWidgetTbl').DataTable({
        ajax: {
            url: "{{ route('client.deal.datatable') }}",
            type: 'POST',
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            data: function (d) {
                d.pipelines = $("#deals-pipelines-select").val()?[$("#deals-pipelines-select").val()]:[],
                d.warnings = $("#deals-warnings-select").val()?[$("#deals-warnings-select").val()]:['AllWarnings'],
                @if ($teams) d.teams = {!! $teams !!}, @endif
                @if ($members) d.members = {!! $members !!} @endif
            }
        },
        dom:"<'row py-1'<'col-sm-12 col-md-8'l><'col-sm-12 col-md-4'f>>" +
            "<'row deals-table-block-row'<'col-sm-12 deals-table-block-col'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        serverSide: true,
        pageLength: 20,
        responsive: false,
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        scrollY:"400px",
        // scrollX: true,
        // scrollCollapse: true,
        scroller:true,
        deferRender:true,
        deferLoading: 1, //loa empty until first refresh
        language: {
            search: "",
            searchPlaceholder: "Search for a deal",
        },
        columns: [
            {data: "id", visible: false, width: '100px'},
            {data: 'name', className: 'deal-eye-cell', render: function (val, type, row) {
                    if (type == 'display') {
                        return '<i class="fa-solid fa-eye editor_edit btn-eye" style="cursor:pointer"></i> '+val;
                    }
                    return val;
                }
            },
            {data: 'warnings', width: '40px', render: function (val, type, row) {
                    if (type == 'display'){
                        let html=''
                        if (val.includes('{{ \App\Enum\DealWarning::LAST_ACTIVITY->value }}')) html+='<i class="fa fa-circle warning-dot" style="color: #ff7b31" data-toggle="tooltip" data-bs-html="true" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="Last activity was too long ago"></i>';
                        if (val.includes('{{ \App\Enum\DealWarning::CLOSE_DATE->value }}')) html+='<i class="fa fa-circle warning-dot" style="color: #ff5656" data-toggle="tooltip" data-bs-html="true" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="Close date has passed"></i>';
                        if (val.includes('{{ \App\Enum\DealWarning::STAGE_TIME_SPEND->value }}')) html+='<i class="fa fa-circle warning-dot" style="color: #ffb526" data-toggle="tooltip" data-bs-html="true" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="Time spent in a stage too long"></i>';
                        if (val.includes('{{ \App\Enum\DealWarning::CREATION_DATE->value }}')) html+='<i class="fa fa-circle warning-dot" style="color: #743af0" data-toggle="tooltip" data-bs-html="true" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="Creation date was too long ago"></i>';
                        return html;
                    }
                    return val;
                }
            },
            {data: 'amount', width:'140px', className: 'cell-bold', render: function (val, type, row) {
                    if (type === 'display') {
                        let euro = Intl.NumberFormat(navigator.language, { style: 'currency', currency: '{{ Auth::user()->currency() }}'});
                        return euro.format(val);
                    }
                    return val;
                }
            },
            {
                data: 'owner_name',
                className: 'deal-widget-owner-cell',
                render: function (val, type, row) {
                    if (type === 'display') {
                        return '<a href="{{ url('/') }}/client/profile/'+row["owner_id"]+'">' +
                                    '<div class="v-avatar member-avatar" style="height: 34px; width: 34px; border: 1px solid #fff!important;" data-toggle="tooltip" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="'+row["owner_firstName"]+' '+row["owner_lastName"]+'">' +
                                    '<span>' + row["owner_firstName"][0] + row["owner_lastName"][0] + '</span>' +
                               '</div></a>';
                    }
                    return val;
                }
            },
            {data: 'status', render: function ( data, type, row ) {
                    return data + ' ('+row['probability']+')';
                }},
            // {data: 'probability'},
            {data: 'kara_probability', render: function (val, type, row) {
                    if ((type === 'display')&&(val)) {
                        return (val*100)+"%";
                    }
                    return val;
                }
            },
            {data: 'closedate'},
            {data: 'hubspot_updatedAt', width:'200px', render: function (val, type, row) {
                    if (type === 'display') {
                        return jQuery.timeago(val);
                    }
                    return val;
                }
            },
        ],
        order: [[1, "desc"]],
        infoCallback: function( settings, start, end, max, total, pre ) {
            $('#DealsPipelineTotal').text(total);
            //return start +" to "+ end+ " of "+total+" entries ";
        },
        initComplete: function () {
            let html = '<label class="col-form-label warnings-title">Warning</label><span id="DealsPipelineTotal" class="warnings-deals-number mx-2"></span>';
            $(html).appendTo( $('.col-md-8:eq(0)', '#dealsWidgetTbl_wrapper.dataTables_wrapper' ) );

            createDealsSelects('deals-warnings-select', @json( \App\Enum\DealWarning::forSelect() ), 'dealsWidgetTbl', 'All Warnings')
                .appendTo( $('.col-md-8:eq(0)', '#dealsWidgetTbl_wrapper.dataTables_wrapper' ) );
            {{--$('#deals-warnings-select option[value="{{ \App\Enum\DealWarning::CLOSE_DATE->value }}"]').css('color','#ff5656' );--}}
            {{--$('#deals-warnings-select option[value="{{ \App\Enum\DealWarning::CLOSE_DATE->value }}"]').css('color','#ff5656' );--}}
            {{--$('#deals-warnings-select option[value="{{ \App\Enum\DealWarning::STAGE_TIME_SPEND->value}}"]').css('color','#ffb526' );--}}
            {{--$('#deals-warnings-select option[value="{{ \App\Enum\DealWarning::CREATION_DATE->value}}"]').css('color','#743af0' );--}}

            $('#deals-warnings-select').select2({
                dropdownAutoWidth: true,
                width: '100%',
                minimumResultsForSearch: -1,
                dropdownCssClass: 'select-default-dropdown'
            });

            let span = '<span class="mx-2">{{__('for')}}</span>';
            $(span).appendTo( $('.col-md-8:eq(0)', '#dealsWidgetTbl_wrapper.dataTables_wrapper' ) );

            createDealsSelects('deals-pipelines-select', @json( $pipelines ), 'dealsWidgetTbl', 'All Pipelines')
                .appendTo( $('.col-md-8:eq(0)', '#dealsWidgetTbl_wrapper.dataTables_wrapper' ) );

            $('#deals-pipelines-select').select2({
                dropdownAutoWidth: true,
                width: '100%',
                minimumResultsForSearch: -1,
                dropdownCssClass: 'select-default-dropdown'
            });

            $('.default-select>.select2-container').addClass('select-default');
            $('#dealsWidgetTbl').DataTable().ajax.reload();
        },
        drawCallback: function() {
            let totalRecords = $('#dealsWidgetTbl').DataTable().data().count();
            if (totalRecords > 4) {
                $("#show-more-deals").css('display', 'flex');
                $('#DealsPipelineTotal').text(totalRecords + ' deals');
            } else {
                $("#show-more-deals").css('display', 'none');
                if (totalRecords > 1) {
                    $('#DealsPipelineTotal').text(totalRecords + ' deals');
                } else {
                    $('#DealsPipelineTotal').text(totalRecords + ' deal');
                }
            }
            let table = $(".dataTables_scrollBody.deal-more");
            if (totalRecords > 0) {
                $("#dealsWidgetTblEmpty").css('display', 'none');
                table.css("max-height", "unset");
                if ($("#deal-more-text").text() === 'Show more') {
                    table.css("max-height", "200px");
                } else {
                    table.css("max-height", "500px");
                }
            } else {
                $("#dealsWidgetTblEmpty").css('display', 'flex');
                table.css("max-height", "0");
            }
        },

        //drawCallback: function( settings ) {
        //    let api = this.api();

            // Output the data for the visible rows to the browser's console
        //    console.log(api.rows({page: 'current'}).data());
        //}
    }).on('click', '.editor_edit', function (e) {// Edit record
        e.preventDefault();

        let data = tableDealsWidget.row( $(this).closest('tr') ).data();
        let id = data["DT_RowId"];

        createModal(
            'Deal details',
            null,
            '{{ url('/') }}/client/deal/'+id+'/edit',
            'modal-lg deal-edit-modal-content',
            function(event){ return onDialogOpen(event, id) },
            function(modal) {
                let form_data = new FormData($('#form-deal-update')[0]);
                let action = $('#form-deal-update').attr('action');
                submitAjaxForm(
                    action,
                    form_data,
                    function(e) {
                        modal.hide();
                        //document.getElementById('deals-overview').dispatchEvent(new CustomEvent('refresh-teams', { detail: {}}));
                        Livewire.dispatchTo(null,'dashboard.new-deals-widget', 'refresh-counter');
                        Livewire.dispatchTo(null,'dashboard.deals-in-progress-widget', 'refresh-counter');
                        Livewire.dispatchTo(null,'dashboard.deals-lost-widget', 'refresh-counter');
                        Livewire.dispatchTo(null,'dashboard.deals-won-widget', 'refresh-counter');
                        Livewire.dispatchTo(null,'dashboard.average-basket-widget', 'refresh-counter');
                        Livewire.dispatchTo(null,'dashboard.total-tasks-widget', 'refresh-counter');
                        Livewire.dispatchTo(null,'dashboard.total-calls-widget', 'refresh-counter');
                        Livewire.dispatchTo(null,'goals.goal-dashboard', 'refresh-dashboard');
                        $('#dealsWidgetTbl').DataTable().ajax.reload();
                    }
                )
            },
            function(e){
                $('body').css('overflow', 'auto');
                //document.getElementById('deals-overview').dispatchEvent(new CustomEvent('refresh-teams', { detail: {}}));
                Livewire.dispatchTo(null,'dashboard.new-deals-widget', 'refresh-counter');
                Livewire.dispatchTo(null,'dashboard.deals-in-progress-widget', 'refresh-counter');
                Livewire.dispatchTo(null,'dashboard.deals-lost-widget', 'refresh-counter');
                Livewire.dispatchTo(null,'dashboard.deals-won-widget', 'refresh-counter');
                Livewire.dispatchTo(null,'dashboard.average-basket-widget', 'refresh-counter');
                Livewire.dispatchTo(null,'dashboard.total-tasks-widget', 'refresh-counter');
                Livewire.dispatchTo(null,'dashboard.total-calls-widget', 'refresh-counter');
                Livewire.dispatchTo(null,'goals.goal-dashboard', 'refresh-dashboard');
                $('#dealsWidgetTbl').DataTable().ajax.reload();
            }
        );
    });

    function createDealsMembersSelect(){
        let teams = new Array($('#teams-select').val());
        $.ajax({
            url: "{{ route('client.member.get') }}",
            type: 'POST',
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            data: { teams : teams }
        })
        .done(function(data) {
            $("#deals-members-select").parent().remove();
            $("#ownedby-span").remove();
            span = '<span class="mx-2" id="ownedby-span">{{__('owned by')}}</span>';
            $(span).appendTo( $('.col-md-8:eq(0)', '#dealsWidgetTbl_wrapper.dataTables_wrapper' ) );
            createDealsSelects('deals-members-select', JSON.parse(data) , 'dealsWidgetTbl', 'All Members')
                .appendTo( $('.col-md-8:eq(0)', '#dealsWidgetTbl_wrapper.dataTables_wrapper' ) );
            let dealsMembersSelect = $('#deals-members-select');
            dealsMembersSelect.select2({
                dropdownAutoWidth: true,
                width: '100%',
                multiple: false,
                dropdownCssClass: 'select-default-dropdown select-dropdown-240',
            });
            $('.default-select>.select2-container').addClass('select-default');
            dealsMembersSelect.one('select2:open', function(e) {
                $('input.select2-search__field').prop('placeholder', 'Search a member');
            });
            $('#dealsWidgetTbl').DataTable().ajax.reload();
        });
    }

    function onDialogOpen(event, id){
        let idx = tableDealsWidget.row('#'+id).index();
        let lastIdx = tableDealsWidget.data().count() - 1;
        let $modalArrows = $('.modal.show .deal-edit-modal-content .modal-content');
        let html = '';
        if (idx>0) html += '<button type="button" class="btn btn-alt-secondary arrow-button arrow-left" title="previous deal" data-next="'+(idx-1)+'" onclick="moveto(this)"><i class="fa-solid fa-arrow-left"></i></button>';
        if (idx!==lastIdx) html += '<button type="button" class="btn btn-alt-secondary arrow-button arrow-right" title="next deal" data-next="'+(idx+1)+'" onclick="moveto(this)"><i class="fa-solid fa-arrow-right"></i></button>';

        $modalArrows.prepend(html);
    }

    function moveto(el){
        let idx = $(el).data('next');
        let form_data = new FormData($('#form-deal-update')[0]);
        let action = $('#form-deal-update').attr('action');
        submitAjaxForm(
            action,
            form_data,
            function(e) {
                let row = tableDealsWidget.row(idx);
                if (row) $(row.node()).find('.editor_edit').click();
            }
        )
    }

    $(document).ready(function() {
        $('.deals-table-block-col>.dataTables_scroll>.dataTables_scrollBody').addClass('deal-more').css('max-height', '200px');
        $('div#dealsWidgetTbl_filter input').addClass('search-input');
        let search_icon = '<span onclick="openDealsSearchBar()" id="search-icon" class="search-icon"><a type="button"><i class="fa fa-search"></i></a></span>';
        $('div#dealsWidgetTbl_filter').prepend(search_icon);
    });

    function openDealsSearchBar() {
        $('div#dealsWidgetTbl_filter').addClass('expand');
        $('div#dealsWidgetTbl_filter input').addClass('search-input-open').focus();
        $('#search-icon').addClass('search-icon-open');
    }

    $(document).on('click', function () {
        if (!$('div#dealsWidgetTbl_filter input').is(":focus")) {
            $('div#dealsWidgetTbl_filter').removeClass('expand');
            $('div#dealsWidgetTbl_filter input').removeClass('search-input-open');
            $('#search-icon').removeClass('search-icon-open');
        }
    });

    function showMoreDeals() {
        let deal_more = $(".deal-more");
        let deal_more_text = $("#deal-more-text");
        let deal_more_icon = $("#deal-more-icon");
        if (deal_more_text.text() === 'Show more') {
            deal_more_text.text("Show less");
            deal_more_icon.removeClass("fa-angle-down");
            deal_more_icon.addClass("fa-angle-up");
            deal_more.css("max-height", "500px");
            deal_more.css("transition", "max-height .5s");
        } else {
            deal_more_text.text("Show more");
            deal_more_icon.removeClass("fa-angle-up");
            deal_more_icon.addClass("fa-angle-down");
            deal_more.css("max-height", "200px");
            deal_more.css("transition", "max-height .5s");
        }
    }
</script>
@endpush
