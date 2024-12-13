@if ($checkout->enrolls->count())
    <ul class="enroll_courses">
        @foreach ($checkout->enrolls as $key => $enroll)
            <li>{{ $key + 1 }}. <a href="{{ route('course.details', $enroll->enrollable->slug) }}"
                    target="_blank">{{ $enroll->enrollable->title }}</a></li>
        @endforeach
    </ul>

    @if ($checkout->enrolls->count() >= 2)
        <button type="button" class="btn btn-sm sg-btn-primary view_all" data-bs-toggle="modal"
            data-bs-target="#enrollCoursesModal_{{ $checkout->id }}">{{ __('View All') }}</button>

        <div class="modal fade" id="enrollCoursesModal_{{ $checkout->id }}" tabindex="-1" role="dialog"
            aria-labelledby="enrollCoursesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <h6 class="sub-title">Courses</h6>
                    <button type="button" class="btn-close modal-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Course Title') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($checkout->enrolls as $key => $enroll)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><a href="{{ route('course.details', $enroll->enrollable->slug) }}"
                                            target="_blank">{{ $enroll->enrollable->title }}</a></td>
                                    <td>
                                        <button type="button" class="btn btn-sm remove-course"
                                            data-id="{{ $enroll->id }}" 
                                            onclick="toggleConfirmButtons({{ $enroll->id }})">
                                            <i class="las la-times"></i>
                                        </button>

                                        <!-- Confirmation buttons -->
                                        <div id="confirmArea_{{ $enroll->id }}" style="display: none;">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="removeEnroll({{ $enroll->id }})">
                                                {{ __('Confirm') }}
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                onclick="toggleConfirmButtons({{ $enroll->id }})">
                                                {{ __('Cancel') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endif