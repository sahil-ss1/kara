<form method="POST" action="{{ route('client.task.update', $task) }}" id="form-task-update">
    @csrf
    @method('PUT')

    <div class="row" style="width:100%;">
        <div class="col">
            <div class="mb-4">
                <label class="form-label" for="deal-select">Deal</label>
                <div class="default-select">
                    <select class="form-select" id="deal-select" name="deal_id" required>
                        <option></option>
                        @foreach( $deals as $key=>$value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label" for="owner-select">Task Owner</label>
                <div class="default-select">
                    <select class="form-select" id="owner-select" name="member_id">
                        <option></option>
                        @foreach( $members as $key=>$value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label" for="hubspot_task_subject">Subject</label>
                <input type="text" class="form-control" name="hubspot_task_subject" id="hubspot_task_subject" placeholder="Subject for this task" value="{{ $task->hubspot_task_subject }}" required/>
            </div>
            <div class="mb-4">
                <label class="form-label" for="hubspot_timestamp">Due date</label>
                <input type="text" class="form-control" id="hubspot_timestamp" name="hubspot_timestamp" placeholder="Choose date" value="{{ $task->hubspot_timestamp }}" required/>
            </div>
            <div class="mb-4">
                <label class="form-label" for="hubspot_task_type">Type</label>
                <div class="default-select">
                    <select class="form-select" name="hubspot_task_type" id="hubspot_task_type">
                        @foreach(\App\Enum\TaskType::cases() as $task_type)
                            <option value="{{ $task_type->value }}" {{ $task->hubspot_task_type->value == $task_type->value ?'selected':'' }}>{{ $task_type->label()  }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label" for="hubspot_task_priority">Priority</label>
                <div class="default-select">
                    <select class="form-select" name="hubspot_task_priority" id="hubspot_task_priority">
                        @foreach(\App\Enum\TaskPriority::cases() as $task_priority)
                            <option value="{{ $task_priority->value }}" {{ $task->hubspot_task_priority->value == $task_priority->value ?'selected':'' }}>{{ $task_priority->value  }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <div class="form-check">
                    <input type="hidden" value="{{ \App\Enum\TaskStatus::NOT_STARTED }}" name="hubspot_status">
                    <input type="checkbox" class="form-check-input" id="hubspot_status" name="hubspot_status"
                           value="{{ \App\Enum\TaskStatus::COMPLETED }}"  {{ $task->hubspot_status == \App\Enum\TaskStatus::COMPLETED->value ?'checked':'' }}>
                    <label class="form-check-label" for="hubspot_status">Completed</label>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label" for="hubspot_task_body">Comments</label>
                <textarea class="form-control" name="hubspot_task_body" id="hubspot_task_body" rows="4" placeholder="Comments for this task">{{ $task->hubspot_task_body }}</textarea>
            </div>
        </div>
    </div>

</form>
<script>
    deal_select = $("#deal-select").select2({
        placeholder: 'Select deal',
        dropdownAutoWidth: true,
        width: '100%',
        dropdownParent: $('#modal1072'),
        dropdownCssClass: 'select-default-dropdown'
    });

    deal_select.one('select2:open', function(e) {
        $('input.select2-search__field').prop('placeholder', 'Search a deal');
    });

    owner_select = $("#owner-select").select2({
        placeholder: 'Select member',
        dropdownAutoWidth: true,
        width: '100%',
        dropdownParent: $('#modal1072'),
        dropdownCssClass: 'select-default-dropdown'
    });

    owner_select.one('select2:open', function(e) {
        $('input.select2-search__field').prop('placeholder', 'Search a member');
    });

    function formatData (data) {
        if (!data.id) { return data.text; }
        let $result = data.text;
        @foreach(\App\Enum\TaskType::cases() as $task_type)
            var task = {!! json_encode($task_type) !!};
            if(task === data.id) {
                $result = $(
                    '<span><img src="{{asset($task_type->icon())}}" class="me-2" width="24" height="24" alt=""> ' + data.text + '</span>'
                );
            }
        @endforeach
        return $result;
    }

    $("#hubspot_task_type").select2({
        dropdownAutoWidth: true,
        width: '100%',
        dropdownParent: $('#modal1072'),
        minimumResultsForSearch: -1,
        dropdownCssClass: 'select-default-dropdown',
        templateResult: formatData,
        templateSelection: formatData
    });

    $("#hubspot_task_priority").select2({
        dropdownAutoWidth: true,
        width: '100%',
        dropdownParent: $('#modal1072'),
        minimumResultsForSearch: -1,
        dropdownCssClass: 'select-default-dropdown'
    });

    $('.default-select>.select2-container').addClass('select-default');

    $('#hubspot_timestamp').dtDateTime();

    @if ($task->deal_id)
        deal_select.val('{{ $task->deal_id }}').trigger('change');
        //$("#deal-select").select2({disabled:'readonly'});
        //$("#deal-select").select2({"readonly": true});
        $("#deal-select").attr("readonly",true).css('cursor', 'not-allowed');
    @endif

    @if ($task->member_id)
        owner_select.val('{{ $task->member_id }}').trigger('change');
        //$("#owner-select").select2({disabled:'readonly'});
        //$("#owner-select").select2({"readonly": true});
        $("#owner-select").attr("readonly",true);
    @endif

    //$("#form-task-update input, #form-task-update select, #form-task-update textarea").change(function() {
    //    $(this).addClass("changed");
    //});

</script>

