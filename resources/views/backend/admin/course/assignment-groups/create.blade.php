@extends('backend.layouts.master')
@section('title', __('Create Assignment Group'))

@section('content')
    <section class="options">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="section-title">{{ __('Create Assignment Group') }}</h3>
                    <div class="bg-white rounded-border p-20 p-sm-30">
                        <form id="assignment-group-form"
                              method="POST"
                              action="{{ route('admin.assignment-groups.store') }}"
                              class="form">
                            @csrf

                            <!-- Group Name -->
                            <div class="mb-4">
                                <label for="name"
                                       class="form-label">{{ __('Group Name') }}</label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       class="form-control"
                                       required>
                                <div class="nk-block-des text-danger">
                                    <p class="name_error error">{{ $errors->first('name') }}</p>
                                </div>
                            </div>

                            <!-- Weight -->
                            <div class="mb-4">
                                <label for="weight"
                                       class="form-label">{{ __('Weight (%)') }}</label>
                                <input type="number"
                                       name="weight"
                                       id="weight"
                                       class="form-control"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       required>
                                <div class="nk-block-des text-danger">
                                    <p class="weight_error error">{{ $errors->first('weight') }}</p>
                                </div>
                            </div>


                            <div>
                                <h5>{{ __('Number of the scores to ignore for each student') }}</h5>
                                <!-- Lowest Scores -->
                                <div class="row">
                                    <div class="col-6 mb-4">
                                        <label for="lowest_degree"
                                               class="form-label">{{ __('Lowest Scores') }}</label>
                                        <input type="number"
                                               name="lowest_degree"
                                               id="lowest_degree"
                                               class="form-control"
                                               min="0"
                                               required>
                                        <div class="nk-block-des text-danger">
                                            <p class="lowest_degree_error error">{{ $errors->first('lowest_degree') }}</p>
                                        </div>
                                    </div>

                                    <!-- Heights Scores -->
                                    <div class="col-6 mb-4">
                                        <label for="highest_degree"
                                               class="form-label">{{ __('Highest Scores') }}</label>
                                        <input type="number"
                                               name="highest_degree"
                                               id="highest_degree"
                                               class="form-control"
                                               min="0"
                                               required>
                                        <div class="nk-block-des text-danger">
                                            <p class="highest_degree_error error">{{ $errors->first('highest_degree') }}</p>
                                        </div>
                                </div>
                                </div>

                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <label for="status"
                                       class="form-label">{{ __('Status') }}</label>
                                <select name="status"
                                        id="status"
                                        class="form-control"
                                        required>
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Inactive') }}</option>
                                </select>
                                <div class="nk-block-des text-danger">
                                    <p class="status_error error">{{ $errors->first('status') }}</p>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end align-items-center mt-30">
                                <button type="submit"
                                        id="submit-btn"
                                        class="btn sg-btn-outline-primary">{{ __('Submit') }}</button>
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