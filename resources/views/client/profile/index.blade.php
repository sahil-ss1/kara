@extends('layouts.app')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <div class="section-row">
                <label class="kara-page-title">{{ $member->id == Auth::user()->member()->first()->id ? __('My profile') : __('Sales profile') }}</label>
                <div class="kara-team-member">
                    <div class="default-select">
                        <select class="form-select" id="profile-members-select">
                            @foreach($members as $value)
                                <option value="{{ $value->id }}" @if($value->id === $member->id) selected @endif>{{ $value->getFullNameAttribute() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-block>
        <div class="row">
            <div class="col-8">
                <div class="section-row">
                    <div class="member-tbl-line">
                        <div class="v-avatar member-avatar me-2" style="height: 68px; min-width: 68px; width: 68px; margin: 0; font-size: 20px">
                            <span> {{ strtoupper(
                                        Str::substr($member->firstName, 0, 1) .
                                        Str::substr($member->lastName, 0, 1)
                                    ) }} </span>
                        </div>
                    </div>
                    <span class="profile-member-name"> {{ $member->getFullNameAttribute() }} </span>
                </div>
            </div>
            <div class="col-4 d-flex align-items-center justify-content-center">
                <div class="section-row">
                    @if($member->id != Auth::user()->member()->first()->id)
                        @isset($meeting)
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="open_meet()" style="border-radius: 12px; padding: 4px 20px">{{__('Instant 1-on-1')}} <i class="fa-solid fa-arrow-right ps-1"></i></button>
                        @else
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="create_meet()" style="border-radius: 12px; padding: 4px 20px"><i class="fa-solid fa-plus pe-1"></i> {{__('Instant 1-on-1')}}</button>
                        @endisset
                    @endif
                </div>
            </div>
        </div>
    </x-block>

    @if($member->active)
        <x-block>
            <livewire:dashboard.dashboard-counters :selected_team="$teams" :member="$member" />
        </x-block>
        <x-deals-grid tableid="modal-deals-grid"></x-deals-grid>

        <x-block>
            <div id="team-goals"
                 x-data="{ teams: [],
                       //period: getStateLocal('goals-period-select-change')?getStateLocal('goals-period-select-change'):'alltime',
                       refresh(){
                            this.teams = new Array($('#teams-select').select2('val'));
                            //saveStateLocal('goals-period-select-change', this.period);
                       }
                     }"
                 x-init="refresh()"
                 @refresh-teams="refresh()"
            >
                <div class="mb-2 section-row" style="justify-content: space-between">
                    <div class="section-title" id="goalDashboardTitle">{{ __('Goals') }}</div>
                    <div>
                        <a class="link-text-btn" href="#" onclick="GoalPersonalBtnClick()">{{ __('Add new personal goals') }}</a>
                        <button type="button" class="btn btn-purple btn-md-purple" onclick="GoalPersonalBtnClick()">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="row goals-completions" id="personal-goals">
                    <livewire:goals.goal-dashboard :member="$member" :goal_team="'null'" :goal_member="$member->id" />
                </div>
                <div class="show-more-wrap mt-2" id="personal-goal-more-wrap" style="display: none">
                    <div onclick="showMoreGoals('personal')"><a type="button" class="show-more" id="personal-goal-more"><i class="fa fa-angle-down pe-2" id="personal-goal-more-icon"></i><span id="personal-goal-more-text">Show more</span></a></div>
                </div>
            </div>
        </x-block>

        <x-block>
            <div class="mb-2 section-row">
                <div class="section-title">{{ __('Tasks') }}</div>
            </div>
            <div class="row mt-3">
                <div class="col-6">
                    @isset($meeting)
                        <x-meeting.todo-grid meeting="{{ $meeting->id }}" />
                    @else
                        <div id="todo_head" class="pb-2">
                            <label class="form-label me-4"><b>{{ __('General') }}</b> <span>(0)</span></label>
                        </div>
                        <div class='empty-table-block' id="teamMembersTblEmpty" style="padding-top: 6px">
                            <div class='empty-table'>
                                <span class='empty-table-text'>No active 1-on-1</span>
                                <img width='21' height='21' src='{{asset('images/emoji-icons/neutral.svg')}}' alt=''>
                            </div>
                        </div>
                    @endisset
                </div>
                <div class="col-6">
                    <livewire:tasks.show-tasks :owner="$member->id" wire:key="meeting-tasks-list"/>
                </div>
            </div>
        </x-block>

        <x-block>
            <div class="row">
                <div class="col warnings">
                    <x-dashboard.deals-widget members="[{{ $member->id }}]"/>
                </div>
            </div>
        </x-block>
    @else
        <x-block>
            <div class="row">
                <div class="col">
                    @if($member->id == Auth::user()->member()->first()->id)
                        {{ __('Your profile is inactive please contact your administrator') }}
                    @else
                        {{ __('This profile is inactive') }}
                    @endif
                </div>
            </div>
        </x-block>
    @endif

@endsection

@section('scripts')
    <script>
        setPageTitle('Profile');
        addBreadcrumbItem('Profile', null);
        $("div.content").addClass("dashboard-page");

        function open_meet() {
            @isset($meeting)
                window.location.href = '{{ url('/').'/client/meeting/' . $meeting->id . '/edit'}}';
            @endisset
        }

        function create_meet() {
            let form_data = new FormData();
            form_data.append('target_id', {{ $member->id }});
            submitAjaxForm(
                '{{ url('/').'/client/meeting' }}',
                form_data,
                (result) => { window.location.href = '{{ url('/').'/client/meeting/' }}'+ result +'/edit'; }
            )
        }

        function GoalPersonalBtnClick() {
            createModal('Manage Goals',
                null,
                '{{ url('/') }}/client/goal/manage/team/null/member/{{ $member->id }}',
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

        function formatProfileData (data) {
            if (!data.id) { return data.text; }
            let $result = data.text;
            @foreach($members as $m)
                if('{!! $m->id !!}' === data.id) {
                    $result = $(
                        '<div class="member-tbl-line"><div class="v-avatar member-avatar me-2" style="height: 28px; min-width: 28px; width: 28px; margin: 0; font-size: 14px"><span>{{ strtoupper(Str::substr($m->firstName,0,1) . Str::substr($m->lastName,0,1)) }}</span></div>{{ $m->getFullNameAttribute() }}</div>'
                    );
                }
            @endforeach

            return $result;
        }

        let profileMembersSelect = $('#profile-members-select');
        profileMembersSelect.select2({
            dropdownAutoWidth: true,
            width: '100%',
            multiple: false,
            dropdownCssClass: 'select-default-dropdown select-dropdown-240',
            templateResult: formatProfileData,
            templateSelection: formatProfileData
        })
            .on('change', function (e) {
                let member = $('#profile-members-select').select2("val");
                window.location.href = '{{ url('/').'/client/profile/' }}'+ member;
            });
            
        $('.default-select>.select2-container').addClass('select-default');

        profileMembersSelect.one('select2:open', function(e) {
            $('input.select2-search__field').prop('placeholder', 'Search a member');
        });

        let tableDeals;
    </script>
@endsection
