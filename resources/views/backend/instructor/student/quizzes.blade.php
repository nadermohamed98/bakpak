@extends('backend.layouts.master')
@section('title', __('all_student'))
@section('content')
    <!-- Organisation Details -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <h3 class="section-title">{{__('Quiz submissions for') }} - {{$user->first_name. ' ' . $user->last_name }}</h3>
                </div>
                <div class="default-tab-list  default-tab-list-v2  bg-white redious-border p-20 p-sm-30">
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
@endpush
