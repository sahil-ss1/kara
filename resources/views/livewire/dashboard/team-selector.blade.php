<div>
    <div class="team-select">
        <div wire:ignore>
            <select class="form-select" id="teams-select">
                <option>None</option>
                @foreach( $teams as $key=>$value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

@push('components_scripts')
    <script>
        document.addEventListener("livewire:initialized", () => {
            let el = $('#teams-select')
            initSelect();
            /*
            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                // Equivelant of 'message.sent'
                succeed(({ snapshot, effect }) => {
                    // Equivelant of 'message.received'
                    queueMicrotask(() => {
                        initSelect()
                    })
                })
                fail(() => {
                    // Equivelant of 'message.failed'
                })
            })
             */
            window.addEventListener('teams-select-change', values => {
                let team;
                try{ team=values.detail[0]['values']; }catch(e){
                    try{ team=values.detail['team']; }catch(e){}
                }
                if (team) {
                    let selected_team = team;
                    el.val(selected_team).trigger('change.select2');//.trigger("change");//
                    saveStateLocal('team-selection-change', new Array(selected_team) );
                    createDealsMembersSelect();
                }else{
                    console.log('No team? Bug?');
                }
                //document.getElementById('deals-overview').dispatchEvent(new CustomEvent('refresh-teams', { detail: {}}));

            })

            el.on('change', function (e) {
                @this.set('selected_team', el.select2("val"));
            })

            function initSelect () {
                function getTeamSelectionFromLocalStorage(){
                    let data = getStateLocal('team-selection-change');
                    if (data) return data.split(',')
                    else return []
                }

                el.select2({
                    placeholder: 'Select team',
                    dropdownAutoWidth: true,
                    width: '100%',
                    multiple: false,
                    minimumResultsForSearch: -1,
                    dropdownCssClass: 'select-team-dropdown'
                })//.val(getTeamSelectionFromLocalStorage()).trigger("change");

                $('.team-select>div>.select2-container').addClass('select-team');

                @this.set('selected_team', getTeamSelectionFromLocalStorage())
            }
        })
    </script>
@endpush
