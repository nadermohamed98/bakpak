document.addEventListener('DOMContentLoaded', function() {
    // Function to update correct answer value
    function updateCorrectAnswer(radioButton) {
        if (!radioButton) return;
        const answerArea = radioButton.closest('.answer_area');
        if (!answerArea) return;
        const answerInputs = answerArea.querySelectorAll('input[name="answers[]"]');
        const inputGroup = radioButton.closest('.input-group');
        if (!inputGroup) return;
        const answerInput = inputGroup.querySelector('input[name="answers[]"]');
        if (!answerInput) return;
        const index = Array.from(answerInputs).indexOf(answerInput);
        radioButton.value = index + 1;
    }

    // Function to add new answer
    function addNewAnswer(questionType) {
        const answerArea = questionType === 'mcq' ?
            document.querySelector('.mcq_div .answer_area') :
            document.querySelector('.default_question_div .answer_area');

        if (!answerArea) return;

        const templateClass = questionType === 'mcq' ? '.checkbox_modal' : '.radio_modal';
        const template = document.querySelector(templateClass);

        if (!template) return;

        const newAnswerHtml = template.innerHTML;
        const newAnswerElement = document.createElement('div');
        newAnswerElement.innerHTML = newAnswerHtml;
        const newAnswer = newAnswerElement.firstElementChild;

        if (!newAnswer) return;

        answerArea.appendChild(newAnswer);

        if (questionType !== 'mcq') {
            const newRadio = newAnswer.querySelector('input[type="radio"]');
            if (newRadio) {
                updateCorrectAnswer(newRadio);
            }
        }
    }

    $(document).on('change', '#question_type', function () {
        var question_type = $(this).val();
        $('.question_div').removeClass('d-none');
        if (question_type == 'mcq') {
            $('.mcq_div').removeClass('d-none');
            $('.default_question_div').addClass('d-none');
        } else if (question_type == 'default') {
            $('.question_div').removeClass('d-none');
            $('.mcq_div').addClass('d-none');
            $('.default_question_div').removeClass('d-none');
        } else {
            $('.question_div').addClass('d-none');
        }
    });

    // Event listener for adding new answer
    document.querySelectorAll('.add_answer').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            if (!form) return;
            const questionTypeSelect = form.querySelector('#question_type');
            if (!questionTypeSelect) return;
            const questionType = questionTypeSelect.value;
            addNewAnswer(questionType);
        });
    });

    // Event delegation for correct answer selection (radio buttons)
    document.addEventListener('change', function(e) {
        if (e.target && e.target.matches('input[type="radio"][name="correct_answer"]')) {
            updateCorrectAnswer(e.target);
        }
    });

    // Event delegation for MCQ correct answer selection (checkboxes)
    document.addEventListener('change', function(e) {
        if (e.target && e.target.matches(
                '.mcq_div .answer_area .custom-checkbox input[type=checkbox]')) {
            const mcqCorrectAnswer = e.target.closest('.input-group')?.querySelector(
                '.mcq_correct_answer');
            if (mcqCorrectAnswer) {
                mcqCorrectAnswer.value = e.target.checked ? '1' : '0';
            }
        }
    });

    // Event delegation for deleting answers
    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.delete_icon')) {
            const inputGroup = e.target.closest('.input-group');
            if (inputGroup) {
                inputGroup.remove();
                // Update correct answer values after deletion
                document.querySelectorAll('input[type="radio"][name="correct_answer"]').forEach(
                    updateCorrectAnswer);
            }
        }
    });

    // Update correct answer values before form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            this.querySelectorAll('input[type="radio"][name="correct_answer"]').forEach(
                updateCorrectAnswer);
        });
    }

    // Initialize Sortable for answer areas
    const answerAreaMoved = document.getElementById('answerAreaMoved');
    if (answerAreaMoved) {
        new Sortable(answerAreaMoved, {
            handle: '.ansMove',
            animation: 150,
            onEnd: function() {
                // Update correct answer values after sorting
                document.querySelectorAll('input[type="radio"][name="correct_answer"]').forEach(
                    updateCorrectAnswer);
            }
        });
    }

    const checkAnswerMoved = document.getElementById('checkAnswerMoved');
    if (checkAnswerMoved) {
        new Sortable(checkAnswerMoved, {
            handle: '.ansMove',
            animation: 150
        });
    }
});