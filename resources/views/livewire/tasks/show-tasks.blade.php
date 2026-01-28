<div>
    <?php $deal_group=null; ?>
    <div class="pb-2">
        <label class="form-label me-4"><b>{{ __('Deal\'s Tasks') }}</b> <span>({{count($tasks)}})</span></label>
        <button type="button" class="btn-purple btn-sm-purple" tabindex="0" data-owner="{{ $owner }}" onclick="create_task(this)"><i class="fa fa-fw fa-plus"></i></button>
    </div>
    <div style="height:280px;overflow:auto;">
    @unless (count($tasks) == 0)
        @foreach($tasks as $task)
            @if ($task->deal_id != $deal_group)
                @if ($deal_group != null) </div></div> @endif
                <div class="block block-mode-hidden deal-related">
                    <div class="heading">
                        <h3 class="block-title deal-name">{{ $task->deal->name }}</h3>
                        <div class="block-options">
                            <button type='button' class="btn btn-outline-secondary btn-round" data-toggle='block-option' data-action='content_toggle'></button>
                            <button type="button" class="btn-purple btn-sm-purple" tabindex="0" data-deal="{{ $task->deal_id }}" data-owner="{{ $owner }}" onclick="create_task(this)"><i class="fa fa-fw fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="block-content">
                    <?php $deal_group=$task->deal_id ?>
            @endif
            <div class="row">
                <div class="col-8"><p>{{ $task->hubspot_task_subject }}</p></div>
                <div class="col-2">
                    @foreach(\App\Enum\TaskType::cases() as $task_type)
                        @if($task->hubspot_task_type === $task_type)
                            <span><img src="{{asset($task_type->icon())}}" width="24" height="24" alt=""></span>
                        @endif
                    @endforeach
                </div>
                <div class="col-2" style="text-align:right">
                    <button type="button" class="btn btn-outline-secondary btn-round" id="dropdown-default-outline-secondary" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-ellipsis more-icon"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end goal-dropdown fs-sm" aria-labelledby="dropdown-default-outline-secondary">
                        <a class="dropdown-item goal-dropdown-item" data-task="{{ $task->id }}" title="Edit" onclick="edit_task(this)"><i class="fa fa-pencil goal-dropdown-icon"></i>Edit</a>
                        <a class="dropdown-item goal-dropdown-item" data-task="{{ $task->id }}" title="Delete" onclick="delete_task(this)"><i class="fa fa-trash goal-dropdown-icon"></i>Delete</a>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class='empty-table-block' id="todoTblEmpty" style="padding-top: 6px">
            <div class='empty-table'>
                <span class='empty-table-text'>There is nothing here</span>
                <img width='21' height='21' src='{{asset('images/emoji-icons/neutral.svg')}}' alt=''>
            </div>
        </div>
    @endunless
    @if ($deal_group != null) </div></div> @endif
    </div>
</div>
@push('scripts')
    <script>
        /*
        Livewire.on('contentChanged', function(e) {
            console.log('event');
            One.block('init');
        });

       document.addEventListener("DOMContentLoaded", () => {
            Livewire.hook('message.processed', (message, component) => {
               //console.log('message.processed:');console.log(message);console.log(component);
                One.block('init', true);One.block('init', true);
            })
       });*/

        document.addEventListener("contentChanged", () => {
            One.block('init');
        });

       function create_task(el){
           let owner = $(el).data('owner');
           let url = '{{ url('/') }}/client/task/create?owner='+owner;
           let dealid = $(el).data('deal');
           if (dealid) url += '&deal='+dealid;

           createModalOverModal(
               '{{__('New task') }}',
               null,
               url,
               'modal-md modal-height-70',
               null,
               function(modal) {
                   submitAjaxFormWithValidation(
                       $('#form-task-create'),
                       function(e) {
                           modal.hide();
                           Livewire.dispatch('refreshTasks');
                       }
                   )
               },
               null,
               '1072',
               '3rem 0 0 0',
               null,
               null
           )
       }

       function edit_task(el){
           let taskid = $(el).data('task');

           createModalOverModal(
               '{{ __('Edit task') }}',
               null,
               '{{ url('/') }}/client/task/'+taskid+'/edit',
               'modal-md modal-height-70',
               null,
               function(modal) {
                   submitAjaxFormWithValidation(
                       $('#form-task-update'),
                       function(e) {
                           modal.hide();
                           Livewire.dispatch('refreshTasks');
                       }
                   )
               },
               null,
               '1072',
               '3rem 0 0 0',
               null,
               null
           )
       }

       function delete_task(el){
           let taskid = $(el).data('task');

           createModal(
               '{{ __('Remove task') }}',
               '{{ __('Are you sure?') }}',
               null,
               'modal-md',
               null,
               function(modal) {
                   let form_data = new FormData();
                   form_data.append('_method', 'DELETE');
                   let action = '{{ url('/') }}/client/task/'+taskid
                   submitAjaxForm(
                       action,
                       form_data,
                       function(e) {
                           modal.hide();
                           Livewire.dispatch('refreshTasks');
                       }
                   )
               },
               null
           )
       }
    </script>
@endpush
