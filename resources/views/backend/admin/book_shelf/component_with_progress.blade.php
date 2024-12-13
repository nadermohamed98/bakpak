@foreach($book_shelfs as $book_shelf)
    <div class="col-xxl-4 col-xl-6 col-lg-12 col-md-6">
        <div class="course-item mb-4" data-aos="fade-up"
             data-aos-delay="100">
            <a href="#" target="_blank" class="course-item-thumb">
                <img
                    src="{{ getFileLink('324x199',$book_shelf->thumbnail) }}"
                    alt="{{ $book_shelf->title }}">
                <span
                    class="course-badge">{{__(@$book_shelf->category->title) }}</span>
            </a>
            <div class="course-item-body">
                <ul class="course-item-info justify-content-end">
                    <li class="rating-review">
                        <span class="total-review"><i class="las la-star"></i> {{ number_format($book_shelf->reviews_avg_rating, 2) }}</span>
                    </li>
                </ul>
                @if(isset($progress_bar))
                    <div class="line-progress mb-12 mt-3" title="{{ $book_shelf->progress }}%">
                        <div data-progress="{{ $book_shelf->progress }}"></div>
                    </div>
                @endif
                <h4 class="title">
                    <a href="{{ route('book_shelf.details',$book_shelf->slug) }}" target="_blank">{{ $book_shelf->title }}</a>
                </h4>

                <ul class="course-item-info">
                    <li>
                        <i class="las la-file-alt"></i> {{ $book_shelf->lessons_count }} {{__('lessons') }}
                    </li>
                    <li>
                        <i class="las la-user-friends"></i> {{ $book_shelf->enrolls_count }} {{__('enroll') }}
                    </li>
                </ul>

            </div>
            <div class="course-item-footer">
                <div class="course-price">{{ $book_shelf->is_free ? __('free') : get_price($book_shelf->price, userCurrency()) }}</div>
                <ul>
                    <li>
                        <a href="{{ route('book_shelf.details', $book_shelf->slug) }}"
                           class="btn btn-sm sg-btn-primary rounded-2">{{__('details') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endforeach
