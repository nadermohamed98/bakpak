@if ($book_shelf->status == 'draft')
    <div class="badge badge-sm badge-warning text-capitalize p-1 py-0">{{ __($book_shelf->status) }}</div>
@elseif ($book_shelf->status == 'in_review')
    <div class="badge badge-sm badge-warning text-capitalize p-1 py-0">{{ $book_shelf->status }}</div>
@elseif ($book_shelf->status == 'approved')
    <div class="badge badge-sm badge-success text-capitalize p-1 py-0">{{ __($book_shelf->status) }}</div>
@elseif ($book_shelf->status == 'rejected')
    <div class="badge badge-sm badge-danger text-capitalize p-1 py-0">{{ __($book_shelf->status) }}</div>
@endif
