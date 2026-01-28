<form method="POST" action="{{ route('client.deal.update', $deal) }}" id="form-deal-update">
    @csrf
    @method('PUT')
    <div class="deal-card">
        <div class="deal-card-header">
            <div class="row" style="width:100%;">
                <div class="col-1">
                    <div class="v-avatar member-avatar my-2" style="height: 34px; width: 34px; margin: 0 12px 0 12px; border: 1px solid #fff!important;" data-toggle="tooltip" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="{{$deal->member->firstName . ' ' . $deal->member->lastName}}">
                        <span>{{ $deal->member->firstName[0] . $deal->member->lastName[0] }}</span>
                    </div>
                </div>
                <div class="col-7">
                    <h6 class="deal-card-title">
                        @php
                            $organization = Auth::user()->organization();
                        @endphp
                        @if($organization)
                            <a href="{{ $organization->getHubspotURL() . '/deal/' . $deal->hubspot_id  }}" target="_blank">{{ $deal->name }}</a>
                        @else
                            <span>{{ $deal->name }}</span>
                        @endif
                    </h6>
                    <p class="deal-card-details">in pipeline {{ $deal->pipeline->label }}</p>
                </div>
                <div class="col-4">
                    <div style="display: flex; justify-content: end">
                    <span style="margin-right:5px; align-self:center;">{{ currency()->getCurrency(Auth::user()->currency())['symbol'] }}</span>
                    <input id="input-amount-value" type="text" value="{{ $deal->amount }}" class="form-control" name="amount" />
                    </div>
                </div>
            </div>
        </div>
        <div class="block-content deal-content">
            <div class="row">
                <div class="col-6" style="align-self:center">
                    <label class="form-label">Started:</label>
                    <span>{{ \Carbon\Carbon::parse($deal->hubspot_createdAt)->format('M jS Y') }}</span>
                </div>
                <div class="col-6">
                    <div class="row">
                        <label class="col-sm-6 col-form-label px-0 text-end" for="closedate" style="align-self:center">Expected Close date:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="closedate" value="{{ \Carbon\Carbon::parse($deal->closedate)->format('Y-m-d') }}" name="closedate"  />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-6"></div>
                <div class="col-6">
                    <div class="row">
                        <label class="col-sm-6 col-form-label px-0 text-end align-self-center" for="kara_probability">Win:</label>
                        <div class="col-sm-6 position-relative d-inline-flex">
                            <input type="number" class="form-control win-percentage-input" id="kara_probability" name="kara_probability" min="0" max="100" value="{{ $deal->kara_probability ? $deal->kara_probability*100 : '' }}" >
                            <div class="kara_probability_symbol">%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="mb-4" id="stage-slider-block">
                    <input type="text" id="stage-slider" class="js-rangeslider" tabindex="-1" name="stage_name"
                           data-values="{{ implode( ',', $stages ) }}"
                           data-from=" {{ $index }}" >
                </div>
            </div>

            <div class="row">
                <div class="col d-flex justify-content-center mt-2 mb-2">
                    <div class="last-activity">
                        <strong>{{ __('Last activity') }}:</strong> {{ \Carbon\Carbon::parse($deal->hubspot_updatedAt)->diffForHumans() }}
                    </div>
                </div>
            </div>

            <div class="row mt-2 mb-1">
                <div class="row col-12">
                    <div class="col-3">
                        <label class="col-form-label" for="hs_manual_forecast_category">{{ __('Forecast Category') }}:</label>
                    </div>
                    <div class="col-9 default-select">
                        <select class="form-select" name="hs_manual_forecast_category" id="hs_manual_forecast_category">
                            <option value=""></option>
                            @foreach($forecast_categories as $forecast_category)
                                <option value="{{ $forecast_category->internal_value }}" {{ $deal->hs_manual_forecast_category == $forecast_category->internal_value ?'selected':'' }}>{{ $forecast_category->label  }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <label class="col-form-label" for="hs_next_step">{{ __('Next step') }}:</label>
                    <textarea class="form-control" id="hs_next_step" name="hs_next_step" rows="3" placeholder="{{ __('Next step') }}..." style="resize: vertical">{{ $deal->hs_next_step ? : '' }}</textarea>
                </div>
            </div>
        </div>
    </div>
</form>
<livewire:grids.activity-grid :deal="$deal" />
<script>
    One.helpersOnLoad(['jq-rangeslider']);
    $('#closedate').dtDateTime();

    changeStageSliderColor($('#stage-slider').data("from"));
    $('#stage-slider').on('change', function (e) {
        changeStageSliderColor($(this).data("from"));
    });

    $('#input-amount-value').css('width', $('#input-amount-value').val().length * 14.4 + 'px')
        .on('input', function (e) {
        let size = $(this).val().length;
        $(this).css('width', size * 14.4 + 'px');
    });

    function changeStageSliderColor(data) {
        if(data % 6 === 0) {
            $('#stage-slider-block').attr("class","stage-slider-stage1");
        } else if (data % 6 === 1) {
            $('#stage-slider-block').attr("class","stage-slider-stage2");
        } else if (data % 6 === 2) {
            $('#stage-slider-block').attr("class","stage-slider-stage3");
        } else if (data % 6 === 3) {
            $('#stage-slider-block').attr("class","stage-slider-stage4");
        } else if (data % 6 === 4) {
            $('#stage-slider-block').attr("class","stage-slider-stage5");
        } else if (data % 6 === 5) {
            $('#stage-slider-block').attr("class","stage-slider-stage6");
        }
    }

    /*
    $('#stage-slider').on('change', function(){
        let $inp = $(this);
        let value = $inp.prop("value");

    });*/
</script>
