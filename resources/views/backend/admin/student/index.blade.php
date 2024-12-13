@extends('backend.layouts.master')
@section('title', __('all_student'))
@section('content')
    <!-- student Details -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">
                        {{ isset($course) && $course->title ? 'Students Enrolled in ' . "($course->title)" : __('student_list') }}
                    </h3>
                    <div class="oftions-content-right">
                    @if(hasPermission('students.create'))
                            <button type="button" class="d-flex align-items-center btn sg-btn-primary gap-2" data-toggle="modal" data-target="#importModal">
                                Import Excel
                            </button>
                            <a href="{{ route('students.create',['courseId'=>$id]) }}"
                               class="d-flex align-items-center btn sg-btn-primary gap-2">
                                <i class="las la-plus"></i>
                                <span>{{__('add') }} {{__('student') }}</span>
                            </a>

                        <div class="modal fade" id="importModal">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h4 class="modal-title">Import Students</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        <form id="importForm" class="form" action="{{ route('students.create_excel') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="file-upload">
                                                <input type="file" id="file" name="file_excel" required>
                                                <input type="hidden" value="{{$id}}" name="courseId">
                                            </div>
                                            <button class="submit-button d-flex align-items-center btn sg-btn-primary gap-2 mt-2" type="submit">Save</button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

            </div>


                <div class="default-tab-list table-responsive default-tab-list-v2  bg-white redious-border p-20 p-sm-30">
                    <!-- End Organisation Details Tab -->
                    <div class="default-list-table yajra-dataTable">
                        {{ $dataTable->table() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('backend.common.delete-script')
@include('backend.common.change-status-script')
@push('js')
    {{ $dataTable->scripts() }}
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#importForm').on('submit', function(event) {
                $("#importModal").modal("hide");
            });
        });
    </script>
@endpush
