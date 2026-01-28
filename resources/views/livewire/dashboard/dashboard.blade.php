<div>
    <div class="row mb-4">
        <div class="col d-inline-flex justify-content-between">
            <div class="section-row">
                <label class="kara-page-title" for="teams-select">{{ __('My team') }}</label>
                <livewire:dashboard.team-selector :teams="$teams" />
                <div class="edit-teams">
                    <button type="button" class="btn btn-outline-primary btn-round js-click-ripple-enabled" id="team-btn">
                        <i class="fa fa-pencil"></i>
                    </button>
                </div>
            </div>
            <div class="d-flex">
                <livewire:dashboard.thumbnail-widget />
            </div>
        </div>
    </div>

    <x-block>
        <livewire:dashboard.dashboard-counters :selected_team="$selected_team" />
    </x-block>

    <x-block>
        <div>
            <div class="mb-2 section-row" style="justify-content: space-between">
                <div class="section-title" id="goalDashboardTitle" for="goals-period-select">{{ __('Team Goals') }}</div>
                <div id='goal-btn'>
                    <a class="link-text-btn" href="#" onclick="GoalBtnClick()">{{ __('Add new team\'s goal') }}</a>
                    <button type="button" class="btn btn-purple btn-md-purple" onclick="GoalBtnClick()">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="row goals-completions" id="team-goals">
                <livewire:goals.goal-dashboard :selected_team="$selected_team" />
            </div>
            <div class="show-more-wrap mt-2" id="team-goal-more-wrap" style="display: none">
                <div onclick="showMoreGoals('team')"><a type="button" class="show-more" id="team-goal-more"><i class="fa fa-angle-down pe-2" id="team-goal-more-icon"></i><span id="team-goal-more-text">Show more</span></a></div>
            </div>
        </div>
    </x-block>

</div>

@push('components_scripts')
    <script>
        $('#team-btn').on('click', function(e){
            createModal('Manage teams',
                null,
                '{{ route('client.team.manage') }}',
                'modal-xl modal-hide-footer',
                null,
                function(dialog){
                    dialog.hide();
                    //document.getElementById('deals-overview').dispatchEvent(new CustomEvent('refresh-teams', { detail: {}}));
                    //window.Livewire.dispatch('teams-select-change', { team:$('#teams-select').select2("val")} )
                    //createDealsMembersSelect();
                    window.location.reload();
                },
                function(){
                    //document.getElementById('deals-overview').dispatchEvent(new CustomEvent('refresh-teams', { detail: {}}));
                    //window.Livewire.dispatch('teams-select-change', { team:$('#teams-select').select2("val")} )
                    //createDealsMembersSelect();
                    window.location.reload();
                },
            );
        });

        function GoalBtnClick() {
            let team = $('#teams-select').select2("val");
            createModal('Manage Goals',
                null,
                '{{ url('/') }}/client/goal/manage/team/'+team+'/member/null',
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

        //let tableDeals;
    </script>
@endpush
