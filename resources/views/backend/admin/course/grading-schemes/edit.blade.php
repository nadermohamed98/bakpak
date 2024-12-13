@extends('backend.layouts.master')
@section('title', __('Edit Grading Scheme'))

@section('content')
    <section class="options">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="section-title">{{ __('Edit Grading Scheme') }}</h3>
                    <div class="bg-white redious-border p-20 p-sm-30">
                        <form id="edit-grading-scheme-form"
                              action="{{ route('admin.grading-schemes.update', $gradingScheme->id) }}"
                              method="POST"
                              class="form">
                            @csrf
                            @method('PUT')

                            <!-- Name Field -->
                            <div class="mb-4">
                                <label for="name"
                                       class="form-label">{{ __('Scheme Name') }}:</label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ $gradingScheme->name }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       required>
								<div class="nk-block-des text-danger">
									<p class="name_error error">{{ $errors->first('name') }}</p>
								</div>
                            </div>

                            <!-- Min Percentage Field -->
                            <div class="mb-4">
                                <label for="min_percentage"
                                       class="form-label">{{ __('Min Percentage') }}:</label>
                                <input type="number"
                                       name="min_percentage"
                                       id="min_percentage"
                                       min="0"
                                       max="100"
                                       step="0.01"
                                       value="{{ $gradingScheme->min_percentage }}"
                                       class="form-control @error('min_percentage') is-invalid @enderror"
                                       required>
								<div class="nk-block-des text-danger">
									<p class="min_percentage_error error">{{ $errors->first('min_percentage') }}</p>
								</div>
                            </div>

                            <!-- Max Percentage Field -->
                            <div class="mb-4">
                                <label for="max_percentage"
                                       class="form-label">{{ __('Max Percentage') }}:</label>
                                <input type="number"
                                       name="max_percentage"
                                       id="max_percentage"
                                       min="0"
                                       max="100"
                                       step="0.01"
                                       value="{{ $gradingScheme->max_percentage }}"
                                       class="form-control @error('max_percentage') is-invalid @enderror"
                                       required>
								<div class="nk-block-des text-danger">
									<p class="max_percentage_error error">{{ $errors->first('max_percentage') }}</p>
								</div>
                            </div>

                            <div class="d-flex justify-content-end align-items-center mt-30">
                                <button type="submit"
                                        id="submit-button"
                                        class="btn sg-btn-outline-primary">{{ __('submit') }}</button>
                                @include('backend.common.loading-btn',['class' => 'btn btn-primary'])
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
@endpush