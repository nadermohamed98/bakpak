@extends('backend.layouts.master')
@section('title', __('Assignment Groups'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{ __('Assignment Groups') }}</h3>
                    <div class="options-content-right mb-12">
                        <a href="{{ route('admin.assignment-groups.create') }}"
                           class="d-flex align-items-center btn sg-btn-primary gap-2">
                            <i class="las la-plus"></i>
                            <span>{{ __('Create New Group') }}</span>
                        </a>
                    </div>
                </div>
                @if($assignmentGroups->count())
                    <div class="bg-white rounded-border p-20 p-sm-30 pt-sm-30">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="default-list-table table-responsive yajra-dataTable">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Weight (%)') }}</th>
                                            <th>{{ __('Lowest Scores') }}</th>
                                            <th>{{ __('Highest Scores') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($assignmentGroups as $group)
                                            <tr id="group-{{ $group->id }}">
                                                <td>{{ $group->name }}</td>
                                                <td>{{ $group->weight }}</td>
                                                <td>{{ $group->lowest_degree }}</td>
                                                <td>{{ $group->highest_degree }}</td>
                                                <td>
                                                    <span class="badge {{ $group->status ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $group->status ? __('Active') : __('Inactive') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <ul class="d-flex gap-30 justify-content-end align-items-center">
                                                        <li>
                                                            <a href="{{ route('admin.assignment-groups.edit', $group->id) }}"><i class="las la-edit"></i></a>
                                                        </li>
                                                        <li>
                                                            <form id="delete-form-{{ $group->id }}"
                                                                  action="{{ route('admin.assignment-groups.destroy', $group->id) }}"
                                                                  method="POST"
                                                                  style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                            <a href="javascript:void(0);"
                                                               onclick="confirmDelete({{ $group->id }})"><i class="las la-trash-alt"></i></a>
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
                    <p class="text-muted">{{ __('No assignment groups found.') }}</p>
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

								// Remove the deleted assignment group row from the table
								let row = document.getElementById('group-' + id);
								if (row) {
									row.parentNode.removeChild(row);
								}
							} else {
								toastr.error('{{ __('Failed to delete the assignment group.') }}');
							}
						})
						.catch(error => {
							// Show error message in toastr
							toastr.error('{{ __('An error occurred while deleting the assignment group.') }}');
						});
				}
			}
    </script>
@endpush