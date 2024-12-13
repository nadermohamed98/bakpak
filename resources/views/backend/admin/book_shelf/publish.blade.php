@if(hasPermission('book_shelf.publish'))
    <div class="setting-check">
        <input type="checkbox" class="pubished_status"
               {{ $book_shelf->is_published == 1 ? 'checked' : '' }} data-id="{{ $book_shelf->id }}"
               value="book_shelf-publish/{{ $book_shelf->id }}" id="customSwitch2-{{ $book_shelf->id }}">
        <label for="customSwitch2-{{ $book_shelf->id }}"></label>
    </div>
@endif
