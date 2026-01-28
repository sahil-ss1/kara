<p>
    <form action="{{ route('admin.user.notify', $user) }}" method="POST" enctype="multipart/form-data" id="modal_notify_form">
        <div class="row mb-2">
            <div class="col">
                <label class="form-label" for="title">{{ __('Title') }}</label>
                <input type="text" class="form-control" id="title" name="title">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label class="form-label" for="message">{{ __('Message') }}</label>
                <textarea  class="form-control" id="message" name="message" rows="4"></textarea>
            </div>
        </div>
    </form>
</p>
