<ul class="d-flex gap-30 justify-content-center align-items-center">
    <li>
        @if(isset($courseId) && !empty($courseId))
            <!-- If courseId is set, include it in the route -->
            <a href="{{ route('admin.students.quizzes', ['userId' => $user->id, 'courseId' => $courseId]) }}">
                <i class="las la-eye"></i>
            </a>
        @else
            <!-- If courseId is not set, omit it from the route -->
            <a href="{{ route('admin.students.quizzes', ['userId' => $user->id]) }}">
                <i class="las la-eye"></i>
            </a>
        @endif
    </li>
</ul>
