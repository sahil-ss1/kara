@extends('layouts.app')

@section('content')
    <x-block title="{{ $meeting->target()->first()->firstName }} 1-on-1" subtitle="{{ $meeting->startAt }}"
             participants="<div class='participants ms-3'>
                               <div class='v-avatar member-avatar' data-toggle='tooltip' data-bs-custom-class='warning-tooltip' data-bs-placement='top' title='{{$meeting->manager()->first()->firstName.' '.$meeting->manager()->first()->lastName}}'>
                                    <span>{{ $meeting->manager()->first()->firstName[0].$meeting->manager()->first()->lastName[0] }}</span>
                               </div>
                               <i class='fa fa-times participants-icon'></i>
                               <div class='v-avatar member-avatar' data-toggle='tooltip' data-bs-custom-class='warning-tooltip' data-bs-placement='top' title='{{$meeting->target()->first()->firstName.' '.$meeting->target()->first()->lastName}}'>
                                    <span>{{ $meeting->target()->first()->firstName[0].$meeting->target()->first()->lastName[0] }}</span>
                               </div>
                           </div>
                           "
             options="<button type='button' class='el-button {{ $meeting->google_event_id ? 'outlined-button' : 'el-button--info' }} orange mb-2' onclick='open_schedule()' id='scheduleBtn' {{ !Auth::user()->google_token ? 'disabled' : '' }}>
                          {{ $meeting->google_event_id ? __('Scheduled') : __('Schedule') }}
                      </button>
                      <button type='submit' class='el-button el-button--info mb-2' form='meeting-form'>{{ __('Submit 1-on-1') }}</button>"
    >
        <form action="{{ route('client.meeting.update', $meeting) }}" method="POST" enctype="multipart/form-data" id="meeting-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="{{ \App\Enum\MeetingStatus::DONE }}">
        </form>
        <div class="row">
            <div class="col">
                <div class="step-number-block">
                    <div class="step-number">1</div>
                    <label class="form-label"><b>{{__('How do you feel?')}}</b></label>
                </div>
                <div class="space-x-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="feeling-bad" name="feeling" value="Bad" form="meeting-form" {{ $meeting->feeling == App\Enum\MeetingFeeling::BAD? 'Checked':'' }}>
                        <label class="form-check-label" for="feeling-bad"><img width="21" height="21" src="{{asset('images/emoji-icons/crossEyes.svg')}}" alt=""></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="feeling-worried" name="feeling" value="Worried" form="meeting-form" {{ $meeting->feeling == App\Enum\MeetingFeeling::WORRIED? 'Checked':'' }}>
                        <label class="form-check-label" for="feeling-worried"><img width="21" height="21" src="{{asset('images/emoji-icons/worriedFace.svg')}}" alt=""></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="feeling-good" name="feeling" value="Good" form="meeting-form" {{ (($meeting->feeling == App\Enum\MeetingFeeling::GOOD)||($meeting->feeling == ''))? 'Checked':'' }}>
                        <label class="form-check-label" for="feeling-good"><img width="21" height="21" src="{{asset('images/emoji-icons/grinning.svg')}}" alt=""></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="feeling-excellent" name="feeling" value="Excellent" form="meeting-form" {{ $meeting->feeling == App\Enum\MeetingFeeling::EXCELLENT? 'Checked':'' }}>
                        <label class="form-check-label" for="feeling-excellent"><img width="21" height="21" src="{{asset('images/emoji-icons/starStruck.svg')}}" alt=""></label>
                    </div>
                </div>
                <textarea class="form-control mt-2" id="textarea-input" name="feeling_note" rows="4" form="meeting-form"
                          placeholder="{{ __('What do you want to share?') }}">{{ $meeting->feeling_note }}</textarea>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <div class="step-number-block">
                    <div class="step-number">2</div>
                    <label class="form-label"><b>{{__('Pipe review')}}</b></label>
                </div>
                <div class="warnings">
                    <x-dashboard.deals-widget members="[{{ $meeting->target()->first()->id }}]"/>
                </div>
                <div class="mt-4">
                    <div class="mb-2 section-row" style="justify-content: space-between">
                        <div class="section-title" id="goalDashboardTitle" for="goals-period-select"><div class="step-number">3</div>{{ __('Team Goals') }}</div>
                        <div>
                            <a id='goal-btn' class="link-text-btn" href="#" onclick="GoalBtnClick()">{{ __('Add new team\'s goal') }}</a>
                            <button type="button" class="btn btn-purple btn-md-purple" onclick="GoalBtnClick()">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row goals-completions" id="team-goals">
                        <livewire:goals.goal-dashboard :selected_team="$teams" :goal_team="'all'" :goal_member="$meeting->target()->first()->id" />
                    </div>
                    <div class="show-more-wrap mt-2" id="team-goal-more-wrap" style="display: none">
                        <div onclick="showMoreGoals('team')"><a type="button" class="show-more" id="team-goal-more"><i class="fa fa-angle-down pe-2" id="team-goal-more-icon"></i><span id="team-goal-more-text">Show more</span></a></div>
                    </div>
                </div>
                <div class="my-4">
                    <div class="mb-2 section-row" style="justify-content: space-between">
                        <div class="step-number-block">
                            <div class="step-number">4</div>
                            <div class="section-title" id="goalDashboardTitle">{{ __('Personal Goals') }}</div>
                        </div>
                        <div>
                            <a id='goal-btn' class="link-text-btn" href="#" onclick="GoalPersonalBtnClick()">{{ __('Add new personal goal') }}</a>
                            <button type="button" class="btn btn-purple btn-md-purple" onclick="GoalPersonalBtnClick()">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row goals-completions" id="personal-goals">
                        <livewire:goals.goal-dashboard :selected_team="$teams" :member="$meeting->target()->first()" :goal_team="'null'" :goal_member="$meeting->target()->first()->id" />
                    </div>
                    <div class="show-more-wrap mt-2" id="personal-goal-more-wrap" style="display: none">
                        <div onclick="showMoreGoals('personal')"><a type="button" class="show-more" id="personal-goal-more"><i class="fa fa-angle-down pe-2" id="personal-goal-more-icon"></i><span id="personal-goal-more-text">Show more</span></a></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-form-label mb-2 step-number-block">
                <div class="step-number">5</div>
                <label class="form-label"><b>{{__('Action plan')}}</b></label>
            </div>
            <div class="col-6">
                <div class="v-avatar member-avatar my-2" data-toggle="tooltip" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="{{$meeting->manager()->first()->firstName.' '.$meeting->manager()->first()->lastName}}">
                    <span>{{ $meeting->manager()->first()->firstName[0].$meeting->manager()->first()->lastName[0] }}</span>
                </div>
                <label for="manager-note-textarea-input"><b>{{ $meeting->manager()->first()->firstName.'\'s '.__('note') }}</b></label>
                <textarea class="form-control" id="manager-note-textarea-input" name="manager_note" rows="4" form="meeting-form"
                          placeholder="{{ __('Key points for this meeting') }}">{{ $meeting->manager_note }}</textarea>
            </div>
            <div class="col-6">
                <div class="v-avatar member-avatar my-2" data-toggle="tooltip" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="{{$meeting->target()->first()->firstName.' '.$meeting->target()->first()->lastName}}">
                    <span>{{ $meeting->target()->first()->firstName[0].$meeting->target()->first()->lastName[0] }}</span>
                </div>
                <label for="target-note-textarea-input"><b>{{ $meeting->target()->first()->firstName.'\'s '.__('note') }}</b></label>
                <textarea class="form-control" id="target-note-textarea-input" name="target_note" rows="4" form="meeting-form"
                          placeholder="{{ __('Main topic and how manager can help you') }}">{{ $meeting->target_note }}</textarea>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-6">
                <x-meeting.todo-grid meeting="{{ $meeting->id }}" />
            </div>
            <div class="col-6">
                <livewire:tasks.show-tasks :owner="$meeting->target_id" wire:key="meeting-tasks-list"/>
            </div>
        </div>
    </x-block>

    <div class="row">
        <div class="col" style="text-align:right;padding-right:30px;">
            <button type="submit" class="el-button el-button--info mb-2" form="meeting-form">{{ __('Submit 1-on-1') }}</button>
        </div>
    </div>

    <x-deals-grid tableid="modal-deals-grid"></x-deals-grid>
@endsection

@section('scripts')
    <script>
        setPageTitle('{{ __('Meet') }}');
        addBreadcrumbItem('1-on-1\'s', '{{ route('client.1-1.index') }}');
        addBreadcrumbItem('{{ __('Meet') }}', null);
        $("div.content").addClass("dashboard-page");

        $('#dealsWidgetTbl').DataTable().ajax.reload();

        function open_schedule(){
            let id = '{{ $meeting->id }}';

            @if ($meeting->google_event_id)
                createModal(
                    '{{ __('1-to-1 Schedule') }}',
                    null,
                    '{{ url('/') }}/client/meeting/'+id+'/schedule/edit',
                    'modal-header-orange',
                    null,
                    null,
                    null
                )
            @else
                createModal(
                    '{{ __('1-to-1 Schedule') }}',
                    null,
                    '{{ url('/') }}/client/meeting/'+id+'/schedule',
                    'modal-header-orange',
                    null,
                    function(modal) {
                        submitAjaxFormWithValidation(
                            $('#form-schedule'),
                            function(e) {
                                modal.hide();
                                window.location.reload();
                            }
                        )
                    },
                    null
                )
            @endif
        }

        function GoalPersonalBtnClick() {
            createModal('Manage Goals',
                null,
                '{{ url('/') }}/client/goal/manage/team/null/member/{{ $meeting->target()->first()->id }}',
                'modal-xl modal-hide-footer',
                null,
                function(dialog){
                    dialog.hide();
                    Livewire.dispatchTo(null,'goals.goal-dashboard', 'refresh-dashboard');
                },
                function(){
                    Livewire.dispatchTo(null,'goals.goal-dashboard', 'refresh-dashboard');
                },
            );
        }

        function GoalBtnClick() {
            createModal('Manage Goals',
                null,
                '{{ url('/') }}/client/goal/manage/team/all/member/{{ $meeting->target()->first()->id }}',
                'modal-xl modal-hide-footer',
                null,
                function(dialog){
                    dialog.hide();
                    Livewire.dispatchTo(null,'goals.goal-dashboard', 'refresh-dashboard');
                },
                function(){
                    Livewire.dispatchTo(null,'goals.goal-dashboard', 'refresh-dashboard');
                },
            );
        }
    </script>
@endsection
