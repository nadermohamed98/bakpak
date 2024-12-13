@php
    if (auth()->user()) {
        $submit_info = App\Models\SubmitedAssignment::where('user_id', auth()->user()->id)->where('assignment_id', $assignment->id)->first();
    }
@endphp
<tr>
    <td>
        <div class="assignment-item">
            <h6>{{__($assignment->title)}}</h6>
            <p>{{__($assignment->instructor->first_name)}} {{__($assignment->instructor->last_name)}}</p>
        </div>
    </td>
    <td>{{ date('d M Y', strtotime($assignment->created_at)) }}</td>
    <td>{{ date('d M Y H:i A', strtotime($assignment->deadline)) }}</td>
</tr>