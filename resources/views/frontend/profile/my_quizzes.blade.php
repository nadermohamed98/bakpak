@extends('frontend.layouts.master')
@section('title', 'My Quizzes')
@section('content')
<style>
    /* Styling for table cells */
    #quizzes-table th, #quizzes-table td {
        padding: 11px 15px;
        text-align: left;
        vertical-align: middle;
    }

    /* Styling for table header */
    #quizzes-table thead th {
        background-color: var(--theme-clr, var(--color-secondary-4));
        color: white;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 14px;
    }

    /* Styling for the table body */
    #quizzes-body tr {
        transition: background-color 0.3s ease;
    }

    #quizzes-body tr:hover {
        background-color: #f8f9fa;
    }

    /* Styling for pagination info */
    #pagination-info {
        font-size: 14px;
        font-weight: 500;
    }

    /* Disabled button styling */
    #pagination-links button:disabled {
        background-color: #6c757d;
        display: flex !important;
        flex-direction: column;
        cursor: not-allowed;
    }

    /* Responsive table styling */
    @media (max-width: 768px) {
        #quizzes-table {
            font-size: 12px;
        }

        #quizzes-table th, #quizzes-table td {
            padding: 8px 10px;
        }
    }
</style>
    <!--====== Start Assignment Section ======-->
    <section class="assignment-section p-t-50 p-b-80 p-b-md-50 p-t-sm-30">
        <div class="container container-1278">
            <div class="row">
                @include('frontend.profile.sidebar')
                <div class="col-md-9">
                    <div class="my-assignment-wrapper">
                        <div class="row icon-boxes-v5 gx-3 gx-md-4 m-b-5 m-b-sm-15">
                            <div class="col-lg-4 col-md-6 col-sm-4">
                                <div class="icon-box m-b-30" data-aos="fade-up" data-aos-delay="200">
                                    <div class="icon-box-icon">
                                        <svg width="46" height="43" viewBox="0 0 46 43" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M29.7081 32.0988C33.6717 32.0988 36.8849 28.8856 36.8849 24.9219C36.8849 20.9583 33.6717 17.7451 29.7081 17.7451C25.7444 17.7451 22.5312 20.9583 22.5312 24.9219C22.5312 28.8856 25.7444 32.0988 29.7081 32.0988Z"
                                                stroke="var(--color-secondary-4)" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M24.9297 30.9023V41.6676L29.7142 38.0792L34.4988 41.6676V30.9023"
                                                  stroke="var(--color-secondary-4)" stroke-width="2"
                                                  stroke-linecap="round" stroke-linejoin="round"/>
                                            <path
                                                d="M17.7459 34.4918H5.78455C4.51561 34.4918 3.29864 33.9877 2.40136 33.0905C1.50408 32.1932 1 30.9762 1 29.7073V5.78455C1 3.15305 3.15305 1 5.78455 1H39.2764C40.5453 1 41.7623 1.50408 42.6595 2.40136C43.5568 3.29864 44.0609 4.51561 44.0609 5.78455V29.7073C44.06 30.5463 43.8386 31.3704 43.4187 32.0968C42.9988 32.8232 42.3953 33.4264 41.6686 33.8459"
                                                stroke="var(--color-secondary-4)" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8.17969 10.5674H36.887" stroke="var(--color-secondary-4)"
                                                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8.17969 17.7451H15.3565" stroke="var(--color-secondary-4)"
                                                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8.17969 24.9219H12.9642" stroke="var(--color-secondary-4)"
                                                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div class="icon-box-content">
                                        <h5 id="quizzes-total"></h5>
                                        <p>Quizzes</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-4">
                                <div class="icon-box m-b-30" data-aos="fade-up" data-aos-delay="400">
                                    <div class="icon-box-icon">
                                        <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M22 42C33.0457 42 42 33.0457 42 22C42 10.9543 33.0457 2 22 2C10.9543 2 2 10.9543 2 22C2 33.0457 10.9543 42 22 42Z"
                                                stroke="var(--color-secondary-4)" stroke-width="2.5"
                                                stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M15.3359 22.0011L19.7804 26.4455L28.6693 17.5566"
                                                  stroke="var(--color-secondary-4)" stroke-width="2.5"
                                                  stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div class="icon-box-content">
                                        <h5 id="quizzes-complete"></h5>
                                        <p>{{__('Passed Quizzes')}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-4">
                                <div class="icon-box m-b-30" data-aos="fade-up" data-aos-delay="600">
                                    <div class="icon-box-icon">
                                        <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M22 42C33.0457 42 42 33.0457 42 22C42 10.9543 33.0457 2 22 2C10.9543 2 2 10.9543 2 22C2 33.0457 10.9543 42 22 42Z"
                                                stroke="var(--color-secondary-4)" stroke-width="2.5"
                                                stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M17.5547 17.5566L26.4436 26.4455M26.4436 17.5566L17.5547 26.4455"
                                                  stroke="var(--color-secondary-4)" stroke-width="2.5"
                                                  stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div class="icon-box-content">
                                        <h5 id="quizzes-failed"></h5>
                                        <p>{{__('In Review')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h6 class="border-bottom-soft-white p-b-5 fw-semibold m-b-5">{{__('My Quiz Results')}}</h6>

                        <div class="assignment-table table-responsive">
                            <!-- Table to display quizzes -->
                            <table id="quizzes-table" class="table table-striped table-hover align-middle" style="min-width: 800px;">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Term</th>
                                        <th>Course</th>
                                        <th>Quiz</th>
                                        <th>Submissions</th>
                                        <th>Status</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody id="quizzes-body" class="border-top-0 assignment_section_wrap"></tbody>
                            </table>
                        
                            <!-- Pagination Info -->
                            <div class="d-flex justify-content-end align-items-center mt-3">
                                <div id="pagination-info" class="text-muted"></div>
                            </div>
                        
                            <!-- Pagination Links -->
                            <div id="pagination-links" class="d-flex justify-content-end mt-3">
                                <!-- Buttons for Previous and Next will be dynamically added here -->
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--====== End Assignment Section ======-->
@endsection
@push('js')
<script>
     function loadQuizzes(page = 1) {
        $.ajax({
            url: '{{ route('my-quizzes') }}', 
            data: {
                user_id: '{{ Auth::user()->id }}', 
                page: page 
            },
            success: function(response) {
                $('#quizzes-body').empty();  // Clear the existing data

                // Loop through each quiz in the response data
                $.each(response.data, function(index, quiz) {
                    // Construct the quiz URL
                    let quizUrl = `/my-quiz/${quiz.slug}`;
                    // Set the status (either 'View' or 'Start quiz')
                    let status = quiz.status == 1 ? 'View' : 'Start quiz';

                    // Format the quiz submission date
                    const formattedDate = quiz.quiz_submission ? 
                        new Date(quiz.quiz_submission).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',   
                            minute: '2-digit'
                        }) : 'Not submitted';

                    // Calculate the question mark and found mark
                    let question_mark = (quiz.total_marks / quiz.questions.count);
                    let found_mark = quiz.pass_marks <= quiz.found_mark ? '<p class="text-success">Pass</p>' : '<p class="text-danger">In Review</p>'; 
                    // Append the quiz row to the table
                    $('#quizzes-body').append(`
                        <tr>
                            <td>${quiz.category_title}</td>
                            <td>${quiz.course_title}</td>
                            <td>${quiz.title}</td>
                            <td>${formattedDate}</td>
                            <td>${found_mark}</td>
                            <td><a href="${quizUrl}">${status}</a></td>
                        </tr>
                    `);
               });

               if (response.total_quizzes) {
                 $('#quizzes-total').html(response.total_quizzes);
               }
               if (response.passed_quizzes) {
                 $('#quizzes-complete').html(response.passed_quizzes);
               }
               
               $('#quizzes-failed').html(response.failed_quizzes);


                // Check if 'from' and 'to' are available in the response
                if (response.pagination.current_page && response.pagination.per_page) {
                    $('#pagination-info').html(`Showing ${response.pagination.current_page} to ${response.pagination.per_page} of ${response.pagination.total} results`);
                } else {
                    $('#pagination-info').html(`Showing 0 to 0 of ${response.pagination.total} results`);
                }

                // Update the pagination links
                $('#pagination-links').html(''); // Clear existing pagination buttons
                $('#pagination-links').append(`
                    <!-- Previous Button -->
                    <button class="template-btn load_more" 
                        ${response.pagination.prev_page_url ? '' : 'disabled'} 
                        onclick="loadQuizzes(${response.pagination.current_page - 1})">Previous</button>

                    <!-- Next Button -->
                    <button class="template-btn load_more" 
                        ${response.pagination.next_page_url ? '' : 'disabled'} 
                        onclick="loadQuizzes(${response.pagination.current_page + 1})">Next</button>

                `);
            }
        });
    }

    $(document).ready(function() {
        loadQuizzes();
    });

</script>
@endpush
