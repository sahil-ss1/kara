<div>
    @unless (count($goals) == 0)
        <div class="row" x-init="
                                    let data = {{count($goals)}};
                                    if(data > 3) {
                                        @if($member)
                                            $('#personal-goal-more-wrap').css('display', 'flex');
                                        @else
                                            $('#team-goal-more-wrap').css('display', 'flex');
                                        @endif
                                    }
                                "
        >
            @foreach($goals as $goal)
                <div class="col-4 mt-2 mb-2">
                    <livewire:goals.goal-dashboard-item
                        :goal="$goal"
                        :wire:key="$goal->id.time()"
                        :goal_team="$goal_team"
                        :goal_member="$goal_member"
                    />
                </div>
            @endforeach
        </div>
    @else
        <div class="section-row mt-3" style="justify-content: space-between">
            <div class="no-goals-message">
                <img width="21" height="21" src="{{asset('images/emoji-icons/sunglasses.svg')}}" alt="">
                <span style="padding-left: 5px">You must set some goals for {{ $member ? 'this member' : 'your team' }} to track its performance</span>
            </div>
        </div>
    @endunless
</div>
@push('components_scripts')
    <script>
        function showMoreGoals (goal_owner) {
            let goal_more = $("#"+goal_owner+"-goals");
            let goal_more_text = $("#"+goal_owner+"-goal-more-text");
            let goal_more_icon = $("#"+goal_owner+"-goal-more-icon");
            if (goal_more_text.text() === 'Show more') {
                goal_more_text.text("Show less");
                goal_more_icon.removeClass("fa-angle-down");
                goal_more_icon.addClass("fa-angle-up");
                goal_more.css("max-height", "334px");
                goal_more.css("transition", "max-height .5s");
            } else {
                goal_more_text.text("Show more");
                goal_more_icon.removeClass("fa-angle-up");
                goal_more_icon.addClass("fa-angle-down");
                goal_more.css("max-height", "160px");
                goal_more.css("transition", "max-height .5s");
            }
        }
    </script>
@endpush

