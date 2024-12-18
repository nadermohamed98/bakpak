<?php

namespace App\Repositories;

use App\Models\QuizQuestion;

class QuizQuestionRepository
{
    protected function parseRequest($data)
    {
        $answers         = [];

        if ($data['question_type'] == 'mcq') {
            foreach ($data['mcq_answers'] as $key => $answer) {
                $answers[] = [
                    'answer'     => $answer,
                    'is_correct' => isset($data['mcq_correct_answer']) ? $data['mcq_correct_answer'][$key] == 1 ? 1 : 0 : 0,
                ];
            }
        } elseif ($data['question_type'] == 'default') {
            foreach ($data['answers'] as $key => $answer) {
                $answers[] = [
                    'answer'     => $answer,
                    'is_correct' => isset($data['correct_answer']) ? $data['correct_answer'] == $key + 1 ? 1 : 0 : 0,
                ];
            }
        }
        elseif ($data['question_type'] == 'short_question') {
            if (empty($data['answers']) || $data['answers'][0] == '') {
                $answers[] = [
                    'answer'     => "",
                    'is_correct' => 1,
                ];
            } else {
                $answers[] = [
                    'answer'     => $data['answers'],
                    'is_correct' => 1,
                ];
            }
        }
        $data['answers'] = $answers;
        return $data;
    }

    public function store($request)
    {
        return QuizQuestion::create($this->parseRequest($request));
    }

    public function find($id)
    {
        return QuizQuestion::find($id);
    }

    public function update($request, $id)
    {
        $questions = QuizQuestion::findOrfail($id);

        return $questions->update($this->parseRequest($request));
    }

    public function destroy($id)
    {
        return QuizQuestion::destroy($id);
    }
}
