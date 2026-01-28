<div class="py-2 px-2 goal-item" x-data="{
                                                goal: JSON.parse('{{ $goal }}'),
                                                metricChange(){ if(this.goal.metric == 'Value') this.goal.type = 'Deal'; },
                                                typeChange(){
                                                    if(this.goal.type != 'Deal') {
                                                        this.goal.metric = 'Count';
                                                    }
                                                    else {

                                                    }
                                                },
                                                pipelineChange(){
                                                    //
                                                },
                                                type_status(type){
                                                    if (type == 'Deal') return {{ json_encode(App\Enum\GoalTypeStatus::values()) }}
                                                    else return ['Created', 'InProgress', 'Closed' ];
                                                },
                                                submit(){
                                                    let form_data = new FormData($refs.form);
                                                    submitAjaxForm($refs.form.action, form_data, function(e){
                                                        $('#goal-edit-btn-{{$goal->id}}').show();
                                                        $('#goal-submit-btn-{{$goal->id}}').hide();
                                                        $('#goal-delete-btn-{{$goal->id}}').show();
                                                    });
                                                },
                                                editGoal(){
                                                    $('#goal-edit-btn-{{$goal->id}}').hide();
                                                    $('#goal-submit-btn-{{$goal->id}}').show();
                                                    $('#goal-delete-btn-{{$goal->id}}').hide();
                                                    $('#goal-value-{{$goal->id}}').css('width', ($('#goal-value-{{$goal->id}}').val().length * 12 + 40) + 'px');
                                                },
                                                setGoalFormDisabled(disabled){
                                                    $('#goal-input-title-{{$goal->id}}').prop('disabled', disabled);
                                                    // $('#goal-owner-{{$goal->id}}').prop('disabled', disabled);
                                                    $('#goal-metric-{{$goal->id}}').prop('disabled', disabled);
                                                    $('#goal-type-{{$goal->id}}').prop('disabled', disabled);
                                                    $('#goal-type-status-{{$goal->id}}').prop('disabled', disabled);
                                                    $('#goal-pipeline-{{$goal->id}}').prop('disabled', disabled);
                                                    $('#goal-stage-{{$goal->id}}').prop('disabled', disabled);
                                                    $('#goal-interval-{{$goal->id}}').prop('disabled', disabled);
                                                    $('#goal-value-{{$goal->id}}').prop('disabled', disabled);
                                                }
                                           }">
    <form action="{{ url('/').'/client/goal/'.$goal->id }}" @submit.prevent="submit(); setGoalFormDisabled(true)" x-ref="form">
        @method('PUT')
        <div class="row mb-2">
            <div class="col-4">
                <input type="text" class="form-control goal-input-title" name="name" x-model="goal.name" id="goal-input-title-{{$goal->id}}" {{$new_goal ? '' : 'disabled'}} />
            </div>
            <div class="col-4">
                <div class="goal-select" id="goal-owner-container">
                    <select class="form-select form-select-sm goal-owner" x-model="goal.member_id" name="member_id" id="goal-owner-{{$goal->id}}" disabled>
                        <option value="" {{ null == $goal->member_id?' selected ':'' }}>No Owner</option>
                        @foreach($members as $key=>$value)
                            <option value="{{ $key }}" {{ $key == $goal->member_id?' selected ':'' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="col" style="display: flex; justify-content: end; vertical-align: middle">
                    <button class="btn btn-sm me-1 goal-edit-btn" type="button" style="{{$new_goal ? 'display: none' : ''}}" @click="editGoal(); setGoalFormDisabled(false)" id="goal-edit-btn-{{$goal->id}}"><i class="fa fa-fw fa-pencil"></i></button>
                    <button class="btn btn-sm btn-success me-1 btn-round" type="submit" style="{{$new_goal ? '' : 'display: none'}}" id="goal-submit-btn-{{$goal->id}}"><i class="fa fa-fw fa-check"></i></button>
                    <button class="btn btn-sm btn-danger me-1 goal-delete-btn" type="button" style="{{$new_goal ? 'display: none' : ''}}" wire:click="$parent.deleteGoal({{$goal->id}})" id="goal-delete-btn-{{$goal->id}}"><i class="fa fa-fw fa-trash"></i></button>
                </div>
            </div>
        </div>

        <div class="row row-cols-lg-auto g-3 align-items-center">
            <div class="col-auto">
                <strong>I'm tracking:</strong>
                <select class="form-select form-select-sm goal-select" x-model="goal.metric" name="metric" style="display:inline-block" @change="metricChange" id="goal-metric-{{$goal->id}}" {{$new_goal ? '' : 'disabled'}}>
                    @foreach(\App\Enum\GoalMetric::cases() as $metric)
                        <option value="{{ $metric->value }}" {{ $metric->value == $goal->metric?' selected ':'' }}>{{ $metric->label() }}</option>
                    @endforeach
                </select>
                <strong>of</strong>
                <select class="form-select form-select-sm goal-select" x-model="goal.type" name="type" style="display:inline-block" @change="typeChange" id="goal-type-{{$goal->id}}" {{$new_goal ? '' : 'disabled'}}>
                    @foreach(\App\Enum\GoalType::cases() as $type)
                        <option value="{{ $type->value }}" {{ $type->value == $goal->type?' selected ':'' }}>{{ $type->value  }}</option>
                    @endforeach
                </select>
                <strong>that</strong>
                <select class="form-select form-select-sm goal-select" x-model="goal.type_status" name="type_status" style="display:inline-block" id="goal-type-status-{{$goal->id}}" {{$new_goal ? '' : 'disabled'}}>
                    <template x-for="value in type_status(goal.type)">
                        <option :value="value" :selected="value==goal.type_status"><span x-text="value"></span></option>
                    </template>
                </select>
            </div>

            <div class="col-auto" x-show="goal.type == 'Deal'">
                <div style="display: inline-block">
                    <strong>in</strong>
                    <select class="form-select form-select-sm goal-select" x-model="goal.pipeline_id" name="pipeline_id" style="display:inline-block" @change="pipelineChange" id="goal-pipeline-{{$goal->id}}" {{$new_goal ? '' : 'disabled'}}>
                        <option value="" {{ null == $goal->pipeline_id?' selected ':'' }}>All Pipelines</option>
                        @foreach($pipelines as $pipeline)
                            <option value="{{ $pipeline->id }}" {{ $pipeline->id == $goal->pipeline_id?' selected ':'' }}>{{ $pipeline->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div :style="goal.type_status != 'Won' ? { display: 'inline-block' } : { display:'none' }">
                    <strong>currently in</strong>
                    <select class="form-select form-select-sm goal-select" x-model="goal.stage_id" name="stage_id" style="display:inline-block"  id="goal-stage-{{$goal->id}}" {{$new_goal ? '' : 'disabled'}}>
                        <option value="" {{ null == $goal->stage_id?' selected ':'' }}>Any stage</option>
                        @foreach($stages as $stage)
                            <option value="{{ $stage->id }}" {{ $stage->id == $goal->stage_id?' selected ':'' }}>{{ $stage->label  }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-auto">
                <select class="form-select form-select-sm goal-select" x-model="goal.interval" name="interval" id="goal-interval-{{$goal->id}}" {{$new_goal ? '' : 'disabled'}}>
                    @foreach(\App\Enum\GoalInterval::cases() as $interval)
                        <option value="{{ $interval->value }}" {{ $interval->value == $goal->interval?' selected ':'' }}>{{ $interval->label()  }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-auto">
                <strong>the target is</strong>
                <input type="number" class="form-control form-control-sm goal-input" x-model="goal.value" name="value" style="width: 124px; display:inline-block" id="goal-value-{{$goal->id}}" {{$new_goal ? '' : 'disabled'}} />
            </div>
        </div>
    </form>
</div>

<script>
    $("#goal-owner-{{$goal->id}}").select2({
        dropdownAutoWidth: true,
        dropdownParent: $('#goal-owner-container'),
        width: '100%',
        multiple: false,
        dropdownCssClass: 'select-goal-dropdown select-dropdown-240'
    });

    $('#goal-value-{{$goal->id}}').on('input', function (e) {
            let size = $(this).val().length;
            $(this).css('width', (size * 12 + 40) + 'px');
        });

    $('.goal-select>.select2-container').addClass('select-goal');
</script>
