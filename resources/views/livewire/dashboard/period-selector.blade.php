<div>
    <div class="default-select">
        <div wire:ignore>
            <select class="form-select" id="period-select">
                @foreach($periods as $key=>$value)
                    <option value="{{ $key }}">{{ __($value) }}</option>
                @endforeach
                <!-- <option>Custom</option> -->
            </select>
        </div>
    </div>
</div>

@push('components_scripts')
    <script>
        document.addEventListener("livewire:initialized", () => {
            let el = $('#period-select');
            initSelect();

            window.addEventListener('dashboard-counters-period-change', values => {
                let selected_period = values.detail[0]['values'];
                el.val(selected_period).trigger('change.select2');//.trigger("change");//
                saveStateLocal('period-select-change', new Array(selected_period) );
                //document.getElementById('deals-overview').dispatchEvent(new CustomEvent('refresh-teams', { detail: {}}));
                @if(!$member)
                    createDealsMembersSelect();
                @endif
            })

            el.on('change', function (e) {
                @this.set('selected_period', el.select2("val"));
            })

            function initSelect () {
                el.select2({
                    placeholder: 'Select period',
                    dropdownAutoWidth: true,
                    width: '100%',
                    multiple: false,
                    minimumResultsForSearch: -1,
                    dropdownCssClass: 'select-default-dropdown'
                });

                $('.default-select>div>.select2-container').addClass('select-default');

                @this.set('selected_period', getStateLocal('period-select-change')?getStateLocal('period-select-change'):'alltime');
            }
        })
    </script>
@endpush
