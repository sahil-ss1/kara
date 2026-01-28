<form method="POST" action="{{ route('client.meeting.schedule.store', $meeting) }}" id="form-schedule">
    @csrf
    <fieldset {{ $event?'disabled':'' }}>
{{--        <div class="block block-rounded">--}}
{{--            <div class="block-header block-header-default">--}}
                <div class="row" style="width:100%;">
                    <div class="col">
                        <div class="mb-4">
                            <label class="form-label" for="title">Title</label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="Title for this meeting" value="{{ $event->summary ?? $meeting->title }}" required/>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="summary">Summary</label>
                            <textarea class="form-control" name="summary" id="summary" rows="4" placeholder="Summary for this meeting">{{ $event->description ?? '' }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="start_date">Date</label>
                            <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Choose date" value="{{ $event ? \Carbon\Carbon::parse($event->getStart()->dateTime)->format('Y-m-d H:i') : '' }}" required/>
                        </div>

                    </div>
                </div>
{{--            </div>--}}
            <div class="block-content"></div>
{{--        </div>--}}
    </fieldset>
</form>

<script>
    $('#start_date').dtDateTime({
        format:'YYYY-MM-DD HH:mm'
    });

    summary = $('#summary');
    summary.text(summary.text().replace("<br>", "\n"));
</script>
