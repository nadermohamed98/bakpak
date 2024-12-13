@if(hasPermission('certificates.edit'))
    <ul class="d-flex gap-30 justify-content-end">
        @if($book_shelf->certificate)
            <li>
                <a href="{{ route('certificates.edit',$book_shelf->id) }}"> <span
                        class="text-success">{{__('manage_certificate')}}</span></a>
            </li>
        @else
            <li>
                <a href="{{ route('certificates.edit',$book_shelf->id) }}"> <span
                        class="text-primary">{{__('add_certificate')}}</span></a>
            </li>
        @endif
    </ul>
@endif
