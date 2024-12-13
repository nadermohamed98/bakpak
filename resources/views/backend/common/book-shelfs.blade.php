<!-- <label for="book-shelfs-pop" class="d-flex align-items-center btn sg-btn-primary gap-2">
    <i class="las la-plus"></i> <span class="file-btn"> {{ __('clone_from_book_shelf') }}</span>
</label>
<input class="d-none" type="hidden" name="{{ $name }}" data-type="{{ $type ?? '' }}" id="book-shelfs-pop" value="{{ old('image') ? old('image') : '' }}">
<div class="{{ $col }} custom-image">
    <div class="selected-files d-flex flex-wrap gap-20">
        @if($book_shelfs)
        <div class="selected-files-item">
            @if (arrayCheck('image_80x80',$book_shelfs) && is_file_exists($book_shelfs['image_80x80'], $book_shelfs['storage']))
            <img src="{{ getFileLink('80x80',$book_shelfs) }}" alt="gallery image" class="selected-img">
            @else
            <img src="{{ static_asset('images/default/default-image-80x80.png') }}" data-default="{{ static_asset('images/default/default-image-80x80.png') }}" alt="category-banner" class="selected-img">
            @endif
            <div class="remove-icon" data-id="shelfs-popup">
                <i class='las la-times'></i>
            </div>
        </div>
        @endif
        <div class="selected-files-item {{ $book_shelfs && arrayCheck('image_80x80',$book_shelfs) && is_file_exists($book_shelfs['image_80x80'], $book_shelfs['storage']) ? 'd-none' : '' }}">
            <img class="selected-img" src="{{ static_asset('images/default/default-image-80x80.png') }}" alt="Headphone">
        </div>
    </div>
</div> -->