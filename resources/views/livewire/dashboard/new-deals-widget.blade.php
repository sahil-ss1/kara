<div class="deals-card"
     x-data="{
        onclick(){
            showDealsGrid(
                '{{ __('New deals') }}',
                {
                  @isset($member) members:[{{ $member->id }}], @endisset
                  teams:(new Array($('#teams-select').val())&&new Array($('#teams-select').val()).length>0)?new Array($('#teams-select').val()):[0],
                  createdate:$('#period-select').val()
                }
            );
        }
     }"
>
    <div class="col" @click="onclick()">
        <h6 class="card-label mb-2">{{ __('New deals') }}</h6>
        <h5 class="money-format mb-2">{{ $amount }}</h5>
        <p class="deal-count">{{ $count }} deals</p>
    </div>
    <img width="28" height="28" src="{{ asset('images/emoji-icons/handshake.svg') }}" alt="" class="deals-card-icon">

</div>
