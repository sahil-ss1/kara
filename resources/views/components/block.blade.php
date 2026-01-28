<div class="block block-rounded @if( isset($hidden)&&$hidden ) block-mode-hidden @endif">
    @isset($title)
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <span class="x-block-title">{{ $title }}</span> <span class="x-block-subtitle ms-2">{{ isset($subtitle)?$subtitle:'' }}</span> @isset($participants){!! $participants !!}@endisset
            </h3>

            @isset($options)
                <div class="block-options">
                    {!! $options !!}
                </div>
            @endisset
            <!--
            <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="pinned_toggle">
                    <i class="si si-pin"></i>
                </button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                    <i class="si si-refresh"></i>
                </button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="close">
                    <i class="si si-close"></i>
                </button>
            </div>
            -->
        </div>
    @endisset
    <div class="block-content block-content-full">
        {{ $slot }}
    </div>
</div>
