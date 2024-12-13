@extends('backend.layouts.master')
@section('title', __('enrollment_history'))
@section('content')
    <section class="options">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="header-top d-flex justify-content-between align-items-center">
                        <h3 class="section-title">{{ __('enrollments') }}</h3>
                        @if (hasPermission('bulk.enrollments'))
                            <div class="options-content-right mb-12">
                                <a href="#" class="d-flex align-items-center btn sg-btn-primary gap-2"
                                    data-bs-toggle="modal" data-bs-target="#student_modal">
                                    <i class="las la-plus"></i>
                                    <span>{{ __('add_students_to_courses') }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="bg-white rounded-border p-20 p-sm-30 pt-sm-30">
                        <div class="row mb-3">
                            <div class="col-lg-4">
                                <select id="filterCourse" class="form-select select2">
                                    <option value="">{{ __('Select Course') }}</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <select id="filterStudent" class="form-select select2">
                                    <option value="">{{ __('Select Student') }}</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->first_name }}
                                            {{ $student->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <select id="filterCategory" class="form-select select2">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="default-list-table table-responsive yajra-dataTable">
                                    {{ $dataTable->table() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if (hasPermission('bulk.enrollments'))
            @include('backend.admin.enrollment.enrollment_modal')
        @endif
        @if (hasPermission('enrollments.status'))
            @include('backend.common.delete-script')
        @endif
    </section>
@endsection
@push('js')
    {{ $dataTable->scripts() }}
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            let table = $('#dataTableBuilder').DataTable();

            $('#filterCourse, #filterStudent, #filterCategory').on('change', function() {
                table.ajax.reload();
            });

            $('#dataTableBuilder').on('preXhr.dt', function(e, settings, data) {
                data.course = $('#filterCourse').val();
                data.student = $('#filterStudent').val();
                data.category = $('#filterCategory').val();
            });
        });

        function toggleConfirmButtons(enrollId) {
            let confirmArea = document.getElementById('confirmArea_' + enrollId);
            if (confirmArea.style.display === 'none' || confirmArea.style.display === '') {
                confirmArea.style.display = 'block';
            } else {
                confirmArea.style.display = 'none';
            }
        }

        function removeEnroll(enrollId) {
            let routeUrl = `/admin/enrollments/${enrollId}`;

            $.ajax({
                url: routeUrl,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add the CSRF token for security
                },
                success: function(response) {
                    if (response.success) {
                        // Remove the course row from the table
                        document.querySelector(`[data-id="${enrollId}"]`).closest('tr').remove();
                        toastr["success"](response.message);
                        window.location.reload();
                    } else {
                        toastr["error"](response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    toastr["error"](response.message);
                }
            });
        }
    </script>
@endpush
