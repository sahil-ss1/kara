@extends('layouts.app')

@section('content')
    <x-block title="Translation" subtitle="List">
        <div class="row">
            <div class="col-4">
                <div class="form-floating mb-4">
                    <select class="form-select" id="languages">
                        @foreach($translations as $language)
                            <option value="{{ $language }}" {{ $language==config('app.locale')?' selected ':'' }}>{{ $language }}</option>
                        @endforeach
                    </select>
                    <label for="languages">{{ __('Language') }}</label>
                </div>
            </div>
        </div>
        <table class="table table-bordered table-striped table-vcenter" id="translationTbl">
            <thead>
            <tr>
                <th>Key</th>
                <th>String</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </x-block>
@endsection

@section('scripts')
    <script>
        setPageTitle('{{ __('Translations') }}');
        addBreadcrumbItem('{{ __('Translations') }}', null);

        let editor = new $.fn.dataTable.Editor( {
            ajax: {
                create: {
                    type: 'POST',
                    url:  '{{ route('admin.translation.store') }}',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                },
                edit: {
                    type: 'PUT',
                    url:  '{{ url('/') }}/admin/translation/{id}',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    data: function (d) {
                        d.language = $('#languages').val();
                    }
                },
                remove: {
                    type: 'DELETE',
                    url:  '{{ url('/') }}/admin/translation/{id}',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    data: function (d) {
                        d.language = $('#languages').val();
                    }
                }
            },
            table: "#translationTbl",
            idSrc:  'key',
            fields: [{
                label: 'Key:',
                name:'key',
                type:'display'
            },{
                label: 'String:',
                name:'string',
                type: 'textarea'
            }],
            formOptions: {
                main: {
                    focus:1,
                },
                inline: {
                    onBlur: 'submit'
                }
            }
        } );

        let tableTranslation = $('#translationTbl').DataTable({
            ajax: {
                url: "{{ route('admin.translation.datatable') }}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                dataSrc: '',
                data: function (d) {
                    d.language = $('#languages').val();
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTable Ajax error:', error, thrown);
                    let errorMsg = 'Failed to load translations';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    alert(errorMsg);
                }
            },
            pageLength: 50,
            responsive: false,
            paging: true,
            lengthChange: false,
            searching: true,
            ordering: false,
            info: true,
            autoWidth: false,
            language: {
                search: "",
                searchPlaceholder: "Search key"
            },
            columns: [
                {data: 'key'},
                {data: 'string', editField:'string'},
                {
                    data: null,
                    className: "text-center",
                    defaultContent: '<div class="btn-group">'+
                        '<button type="button" class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled editor_edit" data-bs-toggle="tooltip" aria-label="Edit"><i class="fa fa-fw fa-pencil-alt"></i></button>'+
                        '<button type="button" class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled editor_remove" data-bs-toggle="tooltip" aria-label="Delete"><i class="fa fa-fw fa-times"></i></button>'+
                        '</div>',
                    responsivePriority: 1,
                    sortable: false,
                    searchable: false,
                    width: '100px'
                }
            ],
        }).on('click', 'button.editor_edit', function (e) {// Edit record
            /*
            let data = tableUsers.row( $(this).closest('tr') ).data();
            let id = data["DT_RowId"];//.substring(4);//data["id"]; //
            window.location.href = '{{ url('/').'/admin/user/' }}'+id+'/edit'
        */

            e.preventDefault();

            editor.edit( $(this).closest('tr'), {
                title: '{{ __('Edit translation') }}',
                buttons: '{{ __('Update') }}'
            });

        }).on('click', 'button.editor_remove', function (e) {// Delete a record
            e.preventDefault();

            editor.remove( $(this).closest('tr'), {
                title: "{{ __('Delete translation key') }}",
                message: "<p>{{ __('Are you sure you wish to remove this translation key?') }}</p>",
                buttons: "{{ __('Delete') }}"
            });
        });

        // Activate an inline edit on click of a table cell
        $('#translationTbl').on( 'click', 'tbody td:not(:first-child)', function (e) {
            editor.inline( this );
        } );

        $('#languages').on('change', function(e){
            $("#translationTbl").DataTable().ajax.reload();
        });

    </script>
@endsection

