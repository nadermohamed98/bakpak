@extends('backend.layouts.master')
@section('title', __('Grading Schemes'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('Grading Schemes') }}</h3>
                    <div class="oftions-content-right mb-12">
                        <a href="{{ route('admin.grading-schemes.create') }}"
                           class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-plus"></i>
                            <span>{{__('create_new_schema') }}</span>
                        </a>
                    </div>
                </div>
                @if($gradingSchemes->count())
                    <div class="bg-white redious-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="default-list-table table-responsive yajra-dataTable">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Min Percentage') }}</th>
                                            <th>{{ __('Max Percentage') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($gradingSchemes as $scheme)
                                            <tr id="scheme-{{ $scheme->id }}">
                                                <td>{{ $scheme->name }}</td>
                                                <td>{{ $scheme->min_percentage }}</td>
                                                <td>{{ $scheme->max_percentage }}</td>
                                                <td>
                                                    <ul class="d-flex gap-30 justify-content-end align-items-center">
                                                        <li>
                                                            <a href="{{ route('admin.grading-schemes.edit', $scheme->id) }}"><i class="las la-edit"></i></a>
                                                        </li>
                                                        <li>
                                                            <form id="delete-form-{{ $scheme->id }}"
                                                                  action="{{ route('admin.grading-schemes.destroy', $scheme->id) }}"
                                                                  method="POST"
                                                                  style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                            <a href="javascript:void(0);"
                                                               onclick="confirmDelete({{ $scheme->id }})"><i class="las la-trash-alt"></i></a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-muted">{{ __('No grading schemes found.') }}</p>
                @endif
            </div>
        </div>
    </div>

    @include('backend.common.delete-script')

    <!-- Display message -->
    <div id="message-container"
         class="alert alert-dismissible"
         style="display:none;"></div>
@endsection

@push('js')
    <script>
	    function confirmDelete(id) {
		    if (confirm('{{ __('Are you sure?') }}')) {
			    // Send AJAX request for deletion
			    let form = document.getElementById('delete-form-' + id);
			    let url = form.action;
			    let token = form.querySelector('input[name="_token"]').value;

			    fetch(url, {
				    method: 'POST',
				    headers: {
					    'Content-Type': 'application/json',
					    'X-CSRF-TOKEN': token
				    },
				    body: JSON.stringify({
					    _method: 'DELETE'
				    })
			    })
				    .then(response => response.json())
				    .then(data => {
					    if (data.message) {
						    toastr.success(data.message);

						    // Remove the deleted grading scheme row from the table
						    let row = document.getElementById('scheme-' + id);
						    if (row) {
							    row.parentNode.removeChild(row);
						    }
					    } else {
						    toastr.error('{{ __('Failed to delete the grading scheme.') }}');
					    }
				    })
				    .catch(error => {
					    // Show error message in toastr
					    toastr.error('{{ __('An error occurred while deleting the grading scheme.') }}');
				    });
		    }
	    }
    </script>
@endpush