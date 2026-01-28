<div class="mb-2">
    @if(!$team || $team=='null' || $team=='all')
        <div class="row mb-2">
            <div class="col-2 goals-team-select">
                <select class="form-select" id="goals-teams-select" wire:model.live="selected_team">
                    <option value="" disabled>{{__('Please select team')}}</option>
                    @foreach( $teams as $key=>$value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col" style="padding-top:5px; max-height: 70vh; overflow-y: auto">
                <div class="goal-header">
                    @if($selected_team)
                        <div class="v-avatar team-avatar"><span>{{ $teams[$selected_team][0] }}</span></div>
                        <span class="goal-header-text">{{ $teams[$selected_team] }}</span>
                    @endif
                    @if($member && $team!='all')
                        <div class="v-avatar member-avatar my-2" style="height: 34px; width: 34px; border: 1px solid #fff!important"><span>{{ $member->firstName[0] . $member->lastName[0] }}</span></div>
                        <span class="goal-header-text">{{ $member->getFullNameAttribute() }}</span>
                    @endif
                </div>
                @foreach( $goals as $goal )
                    <livewire:goals.goal-item :goal="$goal" :wire:key="'goal-item-'.$goal->id" :new_goal_id="$new_goal_id" />
                @endforeach
                <div class="add-goal-wrapper">
                    @if(!$selected_team && ($member || $team=='null'))
                        <div class="select-people mt-3" style="display: block; width: 100%; text-align: center; min-height: 40px">
                            <h3>{{ __('Please select team') }}</h3>
                        </div>
                    @else
                        @if($member)
                            <a id='goal-add-btn' href="#" wire:click="add_personal_goal()">
                                <button type="button" class="btn btn-outline-add-goal">
                                    <i class="fa fa-plus"></i>
                                </button>
                                <span class="goal-add-text">{{ __('Set a new personal goal') }}</span>
                            </a>
                        @else
                            <a id='goal-add-btn' href="#" wire:click="add_team_goal()">
                                <button type="button" class="btn btn-outline-add-goal">
                                    <i class="fa fa-plus"></i>
                                </button>
                                <span class="goal-add-text">{{ __('Set a new team goal') }}</span>
                            </a>
                        @endif
                    @endif
                </div>
        </div>
    </div>
</div>


