<div class="goal-completion"
     x-data="{
        onclick(){
        @if($goal->type == \App\Enum\GoalType::DEAL)
            showDealsGrid(
                '{{ $goal->name }}',
                {
                  @isset($member) members:[{{ $member->id }}], @endisset
                  teams:(new Array($('#teams-select').val())&&new Array($('#teams-select').val()).length>0)?new Array($('#teams-select').val()):[0],
                  'filters':{{ $filters }}
                }
            );
        @endif
        }
     }"
>
    <div class="@if($percentage < 25) goal-red @elseif($percentage < 50) goal-yellow @elseif($percentage < 75) goal-light-blue @else goal-green @endif">
        <div style="display: flex; justify-content: space-between">
            <div class="mb-2 goal-completion-title" style="display: flex">
                <div class="pe-2 @if($goal->type == \App\Enum\GoalType::DEAL) goal-completion-title-hover @endif" @click="onclick()">
                    {{ $goal->name }}
                </div>
                <div>
                    <button type="button" class="tooltip-button-transparent" data-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom"
                            title="<strong>I'm tracking: </strong>
                                    <span class='info-dropdown-value'>{{$goal->metric->label()}}</span>
                                     <i>of</i>
                                    <span class='info-dropdown-value'>{{$goal->type}}</span>
                                    <i>that</i>
                                    <span class='info-dropdown-value'>{{$goal->type_status}}</span>
                                    @if($goal->type == \App\Enum\GoalType::DEAL)
                                        <i>in</i>
                                        <span wire:model.live='goal.pipeline_id' x-model='goal.pipeline_id'>
                                            @if(null == $goal->pipeline_id)
                                                <span class='info-dropdown-value'>All Pipelines</span>
                                            @endif
                                            @foreach($pipelines as $pipeline)
                                                @if($pipeline->id == $goal->pipeline_id)
                                                    <span class='info-dropdown-value'>{{$pipeline->label}}</span>
                                                @endif
                                            @endforeach
                                        </span>

                                        @if($goal->type_status != \App\Enum\GoalTypeStatus::WON)
                                            <i>currently in</i>
                                            <span x-model='goal.stage_id'>
                                                @if(null == $goal->stage_id)
                                                    <span class='info-dropdown-value'>Any stage</span>
                                                @endif
                                                @foreach($stages as $stage)
                                                    @if($stage->id == $goal->stage_id)
                                                        <span class='info-dropdown-value'>{{$stage->label}}</span>
                                                    @endif
                                                @endforeach
                                            </span>
                                        @endif
                                    @endif
                                    <span class='info-dropdown-value'>{{$goal->interval->label()}}</span>
                                    <i>the target is</i>
                                    <span class='info-dropdown-value'>{{ $goal->metric === \App\Enum\GoalMetric::VALUE ? $goal->value : intval($goal->value) }}</span>
                                    @if($goal->member_id)
                                        <i>owner</i>
                                        <span class='info-dropdown-value'>{{ $member->getFullNameAttribute() }}</span>
                                    @endif
                            ">
                        <i class="fa fa-circle-info more-icon"></i>
                    </button>
                </div>
            </div>

            <div class="dropdown" x-data="{
                                                goal: JSON.parse('{{ $goal }}'),
                                                deleteGoal(){
                                                    let form_data = new FormData();
                                                    form_data.append('_method', 'DELETE');
                                                    let url = '{{ url('/').'/client/goal/'.$goal->id }}';
                                                    submitAjaxForm(url, form_data, function(e){
                                                        Livewire.dispatchTo(null,'goals.goal-dashboard', 'refresh-dashboard');
                                                    });
                                                }
                                           }">
                <button type="button" class="btn btn-outline-secondary btn-round" id="dropdown-default-outline-secondary" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis more-icon"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end goal-dropdown fs-sm" aria-labelledby="dropdown-default-outline-secondary">
                    <a class="dropdown-item goal-dropdown-item" @if($goal_team=='null' && $goal_member!='null') onclick="GoalPersonalBtnClick()" @else onclick="GoalBtnClick()" @endif><i class="fa fa-pencil goal-dropdown-icon"></i>Edit</a>
                    <a class="dropdown-item goal-dropdown-item" @click="deleteGoal"><i class="fa fa-trash goal-dropdown-icon"></i>Delete</a>
                </div>
            </div>
        </div>

        <div class="mb-2">
            <div class="goal-interval mb-1">{{ $goal->interval->label() }}</div>
            <div class="progress push mb-1" style="height: 10px;">
                <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;"></div>
            </div>
            <div style="display: flex; justify-content: space-between">
                <div class="progress-details"><strong>{{ $value }}</strong> / {{ $goal->metric === \App\Enum\GoalMetric::VALUE ? $target : intval($target) }}</div>
                <div class="progress-details"><strong>{{ round($percentage) }}</strong>%</div>
            </div>
        </div>
    </div>

</div>
