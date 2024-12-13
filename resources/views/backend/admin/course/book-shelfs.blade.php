@push('css')
    <link rel="stylesheet" href="{{ static_asset('admin/css/custom-clone-course.css') }}">
@endpush

<!-- Modal For Add Media======================== -->
<div class="modal fade" id="book_shelfs" tabindex="-1" aria-labelledby="book_shelfsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="col-lg-12">
                <div class="default-tab-list default-tab-list-v2 media-modal-tab">
                    <ul class="nav pb-12 mb-20" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active ps-0" id="mediaFiles" data-bs-toggle="pill"
                                data-bs-target="#mediaLibraryFiles" role="tab" aria-controls="mediaLibraryFiles"
                                aria-selected="true">{{ __('clone_from_book_shelf') }}</a>
                        </li>
                        <button type="button" class="btn-close modal-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        <li class="ms-auto">
                            <div class="oftions-content-search">
                                <input type="search" name="search" id="search" placeholder="Search">
                                <button type="button"><img src="{{ static_asset('admin/img/icons/search.svg') }}"
                                        alt="{{ __('search') }}"></button>
                            </div>
                        </li>
                    </ul>

                    <!-- End Media Library Tab -->
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="mediaLibraryFiles" role="tabpanel"
                            aria-labelledby="mediaFiles" tabindex="0">
                            <div class="media-flies-wrapper simplebar" id="simplebar">
                                <div class="row gx-20 gy-20" id="media_files">
                                <form action="{{ route('course.clone_from_book_shelf') }}" method="POST" id="courseForm">
                                    @csrf
                                    <input type="hidden" name="book_shelf_id" id="selected_book_shelf_id">
                                    <div class="row gx-20 gy-20">
                                        @foreach($book_shelfs as $book_shelf)
                                            <div class="col-3">
                                                <div class="custom-radio">
                                                    <input type="radio" class="media_selector" data-type="image" data-name="{{ getFileLink('295x248', $book_shelf->image) }}" data-url="{{ getFileLink('295x248', $book_shelf->image) }}" name="book_shelf_id" value="{{ $book_shelf->id }}" id="book_shelf_{{ $book_shelf->id }}">
                                                    <label for="book_shelf_{{ $book_shelf->id }}">
                                                        <div class="media-box">
                                                            <div class="media-card">
                                                                <div class="media-card-thumb">
                                                                    <img src="{{ getFileLink('295x248', $book_shelf->image) }}" alt="{{ $book_shelf->title }}">
                                                                </div>
                                                                <div class="media-card-body">
                                                                    <h6>{{ $book_shelf->title }}</h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </form>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                <div class="d-flex justify-content-end align-items-center mt-30">
                                        <button id="submitFormButton" type="submit" class="btn sg-btn-primary">{{ __('submit') }}</button>
                                        @include('backend.common.loading-btn', ['class' => 'btn sg-btn-primary'])
                                    </div>
                                </div>
                            </div>
                            <!-- End Media File BTN -->
                        </div>
                        <!-- END Upload Media Tab====== -->
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let mediaSelectors = document.querySelectorAll('.media_selector');
        let selectedBookShelfId = document.getElementById('selected_book_shelf_id');

        mediaSelectors.forEach(function(selector) {
            selector.addEventListener('change', function() {
                let selectedId = this.value;
                selectedBookShelfId.value = selectedId;

                document.querySelectorAll('.custom-radio label').forEach(function(label) {
                    label.classList.remove('selected-course');
                });

                this.nextElementSibling.classList.add('selected-course');
            });
        });
    });

    document.getElementById('submitFormButton').addEventListener('click', function() {
        document.getElementById('courseForm').submit();
    });

    const searchInput = document.getElementById('search');
    const mediaFilesContainer = document.getElementById('media_files');
    const books = mediaFilesContainer.getElementsByClassName('col-3');

    searchInput.addEventListener('input', function () {
        const searchTerm = searchInput.value.toLowerCase();
        Array.from(books).forEach(function (book) {
            const title = book.querySelector('.media-card-body h6').innerText.toLowerCase();
            if (title.includes(searchTerm)) {
                book.style.display = '';
            } else {
                book.style.display = 'none';
            }
        });
    });
</script>