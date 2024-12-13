<a href="{{ route('instructor.students.show', $user->id) }}">
    <div class="user-info-panel d-flex gap-12 align-items-center">
        <div class="user-img">
            <img src="{{ getFileLink('40x40', $user->images) }}" alt="{{ $user->first_name }}">
        </div>
    </div>
</a>
