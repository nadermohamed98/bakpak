@extends('frontend.layouts.master')
@section('title', __('instructor'))
@section('content')
    <!--====== Start Instructor Section ======-->
    <section class="instructor-section p-t-50 p-b-80 p-t-sm-30 p-b-sm-50">
        <div class="container container-1278">
            <div class="row">
                <div class="col-12">
                    <div class="section-title-v3 color-dark m-b-40 m-b-sm-30">
                        <h3>{{ __('our_all_instructor') }}</h3>
                        <p class="">{{ __('showing') }} <span class="total_result">{{ $total_results }}</span>
                            {{ __('of') }} <span class="total_instructors">{{ $total_instructors }}</span>
                            {{ __('results') }}</p>
                    </div>
                </div>
            </div>
            <div class="team-member-wrap">
                <div class="row team-member-items-v2 instructor_section_wrap">
                    @if (is_countable($instructors) && count($instructors) > 0)
                        @foreach ($instructors as $instructor)
                            @include('frontend.instructor_load_more')
                        @endforeach
                    @else
                        @include('frontend.not_found', $data = ['title' => 'instructors'])
                    @endif
                </div>
                <div class="m-t-10 text-align-start text-align-md-center">
                    @if ($instructors->nextPageUrl())
                        <div class="instructor-pagination text-align-center text-align-lg-start m-t-sm-10">
                            <a data-page="{{ $instructors->currentPage() }}" href="javascript:void(0)"
                                class="template-btn load_more">{{ __('see_more') }}
                                <i class="fas fa-long-arrow-right"></i></a>
                            @include('components.frontend_loading_btn', [
                                'class' => 'template-btn see-more',
                            ])
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!--====== End Instructor Section ======-->
    <!--====== Start Popular Instructor Section ======-->
    <section class="our-instructor-section color-bg-black p-t-80 p-b-80 p-t-sm-45 p-b-sm-50">
        <div class="container container-1278">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="section-title color-white m-b-35 text-center">
                        <h3>{{ __('popular_instructor') }}</h3>
                        <p>{{ __('The cost of receiving higher education in the United States has skyrocketed to impossible.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row team-items-v1 team-slider slider-primary">
                @if (count($best_teacher) > 0)
                    @foreach ($best_teacher as $instructor)
                        {{ $instructor->instructor }}
                        <div class="col-xl-3 col-lg-4 col-sm-6" data-aos="fade-up" data-aos-delay="{{ 200 * $loop->iteration}}">
                            <div class="team-member-item">
                                <div class="member-img">
                                    <a
                                        href="{{ $instructor->slug ? route('instructor.details', $instructor->slug) : '#' }}"><img
                                            src="{{ getFileLink('128x128', $instructor->images) }}"
                                            alt="{{ $instructor->last_name }}"></a>
                                </div>
                                <div class="member-content">
                                    <h5>
                                        <a
                                            href="{{ $instructor->slug ? route('instructor.details', $instructor->slug) : '#' }}">{{ $instructor->last_name }}
                                            {{ $instructor->last_name }}</a>
                                    </h5>
                                    <p>{{ $instructor->designation }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    @include('frontend.not_found', $data = ['title' => 'best teacher'])
                @endif
            </div>
        </div>
    </section>
    <!--====== End Popular Instructor Section ======-->

    <!--====== Start Best Teachers Section ======-->
    <section class="best-teacher-section p-t-80 p-b-50 p-t-sm-50 p-b-sm-30">
        <div class="container container-1278">
            <div class="row">
                <div class="col-12">
                    <div class="section-title-v3 color-dark m-b-40 m-b-sm-30">
                        <h3>{{ __('best_teacher_of_this_season') }}</h3>
                        <p>{{ __('showing') }} <span class="total_result"> {{ $best_teacher->count() }} </span>
                            {{ __('of') }} <span class="total_instructors"> {{ $best_teacher->count() }}</span>
                            {{ __('results') }}</p>
                    </div>
                </div>
            </div>
            <div class="row team-member-items-v2">
                @if (count($best_teacher) > 0)
                    @foreach ($best_teacher as $teacher)
                        <div class="col-lg-6 col-6" data-aos="fade-up" data-aos-delay="{{ 200 * $loop->iteration}}">
                            <div class="team-member-item">
                                <a href="{{ $teacher->slug ? route('instructor.details', $teacher->slug) : '#' }}"
                                    class="member-img">
                                    <img src="{{ getFileLink('128x128', $teacher->user->images) }}"
                                        alt="{{ $teacher->user->first_name }}">
                                </a>
                                <div class="member-content">
                                    <h5 class="title"><a
                                            href="{{ $teacher->slug ? route('instructor.details', $teacher->slug) : '#' }}">{{ $teacher->user->first_name }}
                                            {{ $teacher->user->last_name }} </a>
                                    </h5>
                                    @if ($teacher->expertises != null)
                                        @php
                                            $expertises = \App\Models\Expertise::whereIn('id', $teacher->expertises)->get();
                                        @endphp
                                        <p>
                                            @foreach ($expertises as $expert)
                                                {{ $expert->title }}
                                            @endforeach
                                        </p>
                                    @endif
                                    <div class="member-footer">
                                        <a href="{{ $teacher->slug ? route('instructor.details', $teacher->slug) : '#' }}"
                                            class="template-btn">{{ __('details') }}</a>
                                        <ul class="social-profile">
                                            <!-- add theme-color-icon class for white bg icon -->
                                            <li><a target="_blank"
                                                    href="{{ arrayCheck('facebook', $teacher->social_links) ? $teacher->social_links['facebook'] : '#' }}"><i
                                                        class="fab fa-facebook-f"></i></a></li>
                                            <li><a target="_blank"
                                                    href="{{ arrayCheck('twitter', $teacher->social_links) ? $teacher->social_links['twitter'] : '# ' }}"><i
                                                        class="fab fa-twitter"></i></a></li>
                                            <li><a target="_blank"
                                                    href="{{ arrayCheck('linkedin', $teacher->social_links) ? $teacher->social_links['linkedin'] : '#' }}"><i
                                                        class="fab fa-linkedin-in"></i></a></li>
                                            <li><a target="_blank"
                                                    href="{{ arrayCheck('instagram', $teacher->social_links) ? $teacher->social_links['instagram'] : '#' }}"><i
                                                        class="fab fa-instagram"></i></a></li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    @include('frontend.not_found', $data = ['title' => 'best teacher'])
                @endif
            </div>
        </div>
    </section>
    <!--====== End Best Teachers Section ======-->

    <!--====== Start Become Instructor Section ======-->
    @if(setting('become_instructor_status') == 1 )
    <section class="become-instructor-section color-bg-off-white p-t-60 p-b-70 p-t-sm-40 p-b-sm-50 position-relative">
        <div class="bg-shape">
            <svg width="482" height="424" viewBox="0 0 482 424" fill="none" xmlns="http://www.w3.org/2000/svg">
                <mask id="mask0_1932_5807" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                    width="482" height="424">
                    <rect width="482" height="424" transform="matrix(1 0 0 -1 0 424)" fill="#F6C32C" />
                </mask>
                <g mask="url(#mask0_1932_5807)">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M364.139 318.615C378.332 334.581 403.157 336.343 419.587 322.551C436.016 308.76 437.83 284.637 423.637 268.671L279.893 106.974C265.701 91.0082 240.876 89.246 224.446 103.038C208.016 116.829 206.203 140.952 220.396 156.918L364.139 318.615ZM-35.4788 305.221C-21.286 321.186 3.53859 322.948 19.9684 309.157C36.3982 295.365 38.2117 271.242 24.0189 255.277L-119.725 93.5792C-133.918 77.6137 -158.742 75.8515 -175.172 89.6432C-191.602 103.435 -193.415 127.558 -179.222 143.523L-35.4788 305.221ZM-47.5486 146.264C-33.3558 162.229 -8.53124 163.991 7.89859 150.2C24.3284 136.408 26.1418 112.285 11.949 96.3195L-131.795 -65.3778C-145.987 -81.3433 -170.812 -83.1055 -187.242 -69.3138C-203.672 -55.5221 -205.485 -31.3991 -191.292 -15.4336L-47.5486 146.264ZM211.075 87.9965C194.645 101.788 169.82 100.026 155.628 84.0605L11.884 -77.6368C-2.30884 -93.6023 -0.495379 -117.725 15.9344 -131.517C32.3643 -145.309 57.1888 -143.546 71.3816 -127.581L215.125 34.1163C229.318 50.0818 227.505 74.2048 211.075 87.9965ZM413.366 170.174C396.937 183.966 372.112 182.204 357.919 166.238L214.175 4.54098C199.983 -11.4245 201.796 -35.5475 218.226 -49.3392C234.656 -63.1309 259.48 -61.3687 273.673 -45.4032L417.417 116.294C431.61 132.26 429.796 156.383 413.366 170.174ZM162.635 382.699C176.827 398.665 201.652 400.427 218.082 386.635C234.512 372.844 236.325 348.721 222.132 332.755L78.3885 171.058C64.1957 155.092 39.3711 153.33 22.9413 167.122C6.51147 180.913 4.69802 205.036 18.8908 221.002L162.635 382.699ZM194.73 214.989C178.3 228.78 153.476 227.018 139.283 211.053L-4.46075 49.3554C-18.6536 33.3899 -16.8401 9.26696 -0.410278 -4.52476C16.0195 -18.3165 40.8441 -16.5543 55.0369 -0.588768L198.781 161.109C212.973 177.074 211.16 201.197 194.73 214.989Z"
                        fill="#7CB799" fill-opacity="0.2" />
                </g>
            </svg>
        </div>

        <div class="container container-1278">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-7" data-aos="fade-up" data-aos-delay="200">
                    <div class="become-instructor-text-block m-b-40">
                        <div class="common-heading">
                            <h3 class="m-b-15">{{ setting('become_instructor_title') }}</h3>
                            <p class="m-b-25">{{ setting('become_instructor_description') }}</p>
                            <a href="{{ route('instructor.sign_up') }}" class="template-btn">{{ __('start_now') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-5"data-aos="fade-up" data-aos-delay="400">
                    <div class="become-instructor-image">
                        <img src="{{ getFileLink('391x541',setting('become_instructor_image')) }}" alt="Become Instructor Image">
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.load_more', function() {
                instructor('load_more');
            });

            function instructor(load_more) {
                var that = $('.load_more');
                if (load_more == 'load_more') {
                    page = parseInt(that.data('page')) + 1;
                }
                var btn_selector = $('.instructor-pagination');
                btn_selector.find('.loading_button').removeClass('d-none');
                that.addClass('d-none');


                $.ajax({
                    url: "{{ route('instructors') }}",
                    type: "GET",
                    data: {
                        page: page,
                    },
                    success: function(data) {
                        let selector = $('.instructor_section_wrap');
                        selector.append(data.instructors);

                        $('.total_result').html(data.total_results);
                        $('.total_instructors').html(data.total_instructors);
                        that.data('page', page);
                        initAOS();
                        activeNiceSelect();
                        if (data.next_page) {
                            btn_selector.find('.loading_button').addClass('d-none');
                            that.removeClass('d-none');
                        } else {
                            btn_selector.find('.loading_button').addClass('d-none');
                            that.addClass('d-none');
                        }

                    }
                });
            }
        })
    </script>
@endpush
