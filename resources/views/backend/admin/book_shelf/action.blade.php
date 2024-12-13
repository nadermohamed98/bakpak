<ul class="d-flex gap-30 justify-content-end align-items-center">
    @if(hasPermission('book_shelf.edit'))
        <li>
            <a href="{{ route('book_shelf.edit', $book_shelf->id) }}"><i class="las la-edit"></i></a>
        </li>
    @endif

    @if(hasPermission('book_shelf.delete'))
    <li>
        <a href="javascript:void(0)"
           onclick="delete_row('{{ route('book_shelf.destroy', $book_shelf->id) }}')"
           data-toggle="tooltip"
           data-original-title="{{ __('delete') }}"><i class="las la-trash-alt"></i></a>
    </li>
    @endif    
</ul>

