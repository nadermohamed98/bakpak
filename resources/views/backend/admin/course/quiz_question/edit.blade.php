<form action="{{ route('quiz-questions.update',$question->id) }}" method="POST" class="form">@csrf
    @method('PUT')
    <div class="row gx-20">
        <input type="hidden" value="{{ $quiz->id }}" name="quiz_id">
        <input type="hidden" value="0" class="is_modal">
        <div class="col-12">
            <div class="mb-4">
                <label for="question_type" class="form-label">Question Type</label>
                <div class="select-type-v2">
                    <select id="question_type" name="question_type"
                           class="form-control form-select-lg mb-3 without_search">
                            <option value="default" {{ $question->question_type == 'default' ? 'selected' : '' }}>Multiple Choice
                            </option>
                            <option value="mcq" {{ $question->question_type == 'mcq' ? 'selected' : '' }}>Multiple Answer
                                Question
                            </option>
                        <option value="short_question" {{ $question->question_type == 'short_question' ? 'selected' : '' }}>
                            Short Question
                        </option>
                        <option value="text_question" {{ $question->question_type == 'text_question' ? 'selected' : '' }}>
                            Text Question
                        </option>
                    </select>
                    <div class="nk-block-des text-danger">
                        <p class="question_type_error error"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- End Question Type -->
        <div class="col-12">
            <div class="mb-4">
                <label for="question" class="form-label">Question</label>
                <textarea name="question" class="summernote rounded-2" id="question" placeholder="Enter Question">{!! $question->question !!}</textarea>
                <div class="nk-block-des text-danger">
                    <p class="question_error error"></p>
                </div>
            </div>
        </div>

        @if($question->question_type == 'short_question')
            <div class="col-lg-12">
                <div class="mb-4">
                    <label class="form-label">{{ __('Answer') }}</label>
                    <textarea class="summernote" name="answers" placeholder="{{ __('Enter Answer') }}"> {!! isset($question->answers[0]['answer']) ? $question->answers[0]['answer'] : '' !!}</textarea>
                </div>
            </div>
        @else

        <div class="col-12 question_div {{ $question->question_type == 'text_question' ? 'd-none' : '' }} ">
                <div class="">
                    <label for="#" class="form-label">Quiz Answers</label>
                    <div
                            class="moveable-lit p-30 default_question_div {{ $question->question_type == 'mcq' ? 'd-none' : '' }} rounded-2 border">
                        <div class="answer_area" id="answerAreaMoved">
                            @foreach($question->answers as $key=> $answer)
                                <div class="input-group mb-20">
                                    <input type="text" class="form-control" name="answers[]"
                                           placeholder="Answer" value="{{ $answer['answer'] }}">
                                    <div class="custom-radio">
                                        <label>
                                            <input type="radio" name="correct_answer"
                                                   value="{{ ++$key }}" {{ $answer['is_correct'] == 1 ? 'checked' : 0 }}>
                                            <span class=""></span>
                                        </label>
                                        <span class="icon delete_icon"><i class="las la-trash-alt"></i></span>
                                    </div>
                                    {{--                                <span class="input-group-text"><i class="las la-trash-alt ml-3"></i></span>--}}
                                    <span class="input-group-text ansMove"><i class="las la-arrows-alt"></i></span>
                                </div>
                            @endforeach
                        </div>
                        <div class="nk-block-des text-danger">
                            <p class="answers_error error"></p>
                        </div>
                        <div class="nk-block-des text-danger">
                            <p class="correct_answer_error error"></p>
                        </div>
                        <div class="d-flex justify-content-start align-items-center mt-30">
                            <button type="button" class="btn sg-btn-outline-primary add_answer">Add Answer
                            </button>
                        </div>
                    </div>
                    <div
                            class="moveable-lit p-30 mcq_div {{ $question->question_type == 'mcq' ? '' : 'd-none' }} rounded-2 border">
                        <div class="answer_area">
                            @foreach($question->answers as $key=> $answer)
                                <div class="input-group mb-20">
                                    <input type="hidden" name="mcq_correct_answer[]" class="mcq_correct_answer" value="{{ $answer['is_correct'] }}">
                                    <input type="text" class="form-control" name="mcq_answers[]"
                                           placeholder="Answer" value="{{ $answer['answer'] }}">
                                    <div class="custom-checkbox">
                                        <label>
                                            <input type="checkbox" value="1" {{ $answer['is_correct'] == 1 ? 'checked' : '' }}>
                                            <span class="ms-12"></span>
                                        </label>
                                        <span class="icon delete_icon"><i class="las la-trash-alt"></i></span>
                                    </div>
                                    <span class="input-group-text"><i class="las la-arrows-alt"></i></span>
                                </div>
                            @endforeach
                        </div>
                        <div class="nk-block-des text-danger">
                            <p class="mcq_answers_error error"></p>
                        </div>
                        <div class="nk-block-des text-danger">
                            <p class="mcq_correct_answer_error error"></p>
                        </div>
                        <div class="d-flex justify-content-start align-items-center mt-30">
                            <button type="button" class="btn sg-btn-outline-primary add_answer">Add Answer
                            </button>
                            <!-- <button type="button" class="btn sg-btn-primary">Add Question</button> -->
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- End Question -->

    </div>
    <div class="d-flex justify-content-between align-items-center mt-30">
        <button type="button" class="btn sg-btn-outline-primary" data-bs-dismiss="modal"
                aria-label="Close">Cancel
        </button>
        <button type="submit" class="btn sg-btn-primary">Update Question</button>
        @include('backend.common.loading-btn',['class' => 'btn sg-btn-primary'])
    </div>
</form>
