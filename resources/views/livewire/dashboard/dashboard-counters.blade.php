<div>
    <div id="deals-overview">
        <div class="mb-4 section-row">
            <div class="section-title">{{ __('Numbers') }}<span class="section-subtitle">{{ __('from') }}</span></div>
            <livewire:dashboard.period-selector :periods="$periods" :member="$member" />
        </div>

        <div class="row mb-4">
            <div class="col-3">
                <livewire:dashboard.new-deals-widget :selected_team="$selected_team" :selected_period="$selected_period" :member="$member" />
            </div>
            <div class="col-3">
                <livewire:dashboard.deals-in-progress-widget :selected_team="$selected_team" :selected_period="$selected_period" :member="$member" />
            </div>
            <div class="col-3">
                <livewire:dashboard.deals-lost-widget :selected_team="$selected_team" :selected_period="$selected_period" :member="$member" />
            </div>
            <div class="col-3">
                <livewire:dashboard.deals-won-widget :selected_team="$selected_team" :selected_period="$selected_period" :member="$member" />
            </div>
        </div>

        <div class="row">
            <div class="col-4">
                <livewire:dashboard.average-basket-widget :selected_team="$selected_team" :selected_period="$selected_period" :member="$member" />
            </div>
            <div class="col-4">
                <livewire:dashboard.total-tasks-widget :selected_team="$selected_team" :selected_period="$selected_period" :member="$member" />
            </div>
            <div class="col-4">
                <livewire:dashboard.total-calls-widget :selected_team="$selected_team" :selected_period="$selected_period" :member="$member" />
            </div>
        </div>
    </div>
</div>
