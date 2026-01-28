<div class="deals-card"
     x-data="{
        onclick(){
            showDealsGrid(
                '{{ __('Deals lost') }}',
                {
                  @isset($member) members:[{{ $member->id }}], @endisset
                  teams:(new Array($('#teams-select').val())&&new Array($('#teams-select').val()).length>0)?new Array($('#teams-select').val()):[0],
                  probability:0,
                  closedate:$('#period-select').val()
                }
            );
        }
     }"
>
    <div class="col" @click="onclick()">
        <h6 class="card-label mb-2 deals-lost-title">{{ __('Deals lost') }}</h6>
        <h5 class="money-format mb-1 deals-lost-title">{{ $amount }}</h5>
        <p class="deal-count">{{ $count }} deals</p>
    </div>
    <img width="28" height="28" src="{{asset('images/emoji-icons/worriedFace.svg')}}" alt="" class="deals-card-icon">

</div>



