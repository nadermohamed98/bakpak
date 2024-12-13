<?php

namespace App\DataTables\Instructor;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class StudentQuizzesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('quiz_name', function ($quiz) {
                return $quiz->title; // Display the quiz title
            })
            ->addColumn('course_name', function ($quiz) {
                return $quiz->course_title; // Display the course title from the join
            })
            ->addColumn('quiz_submission', function ($quiz) {
                return Carbon::parse($quiz->quiz_submission)->format('m/d/Y, g:i a');
            })
            ->addColumn('category_name', function ($quiz) {
                return $quiz->category_title; // Display the section title from the join
            })
            ->addColumn('quiz_details', function ($quiz) {
                $quizSlug = encrypt($quiz->id);
                $userId = $this->user_id;                 
                if (auth()->user()->user_type == 'instructor') {
                    return view('backend.instructor.student.quiz_details', compact('quizSlug', 'userId'));
                } elseif (auth()->user()->user_type == 'admin') {
                    return view('backend.admin.student.quiz_details', compact('quizSlug', 'userId'));
                }
            })
            ->setRowId('id');
    }
    

    public function query(): QueryBuilder
    {
        $search = $this->request->search['value'];
                
        $quizzes = Quiz::select(
            'quizzes.*',
            'courses.title as course_title',
            'sections.title as section_title',
            'categories.title as category_title',
            'quiz_answers.updated_at as quiz_submission'
        )
        ->distinct()
        ->join('sections', 'sections.id', '=', 'quizzes.section_id')
        ->join('courses', 'courses.id', '=', 'sections.course_id')
        ->join('categories', 'categories.id', '=', 'courses.category_id')
        ->join('quiz_questions', 'quiz_questions.quiz_id', '=', 'quizzes.id')
        ->join('quiz_answers', 'quiz_answers.quiz_question_id', '=', 'quiz_questions.id')
        ->where('quiz_answers.user_id', $this->user_id)
        ->when($search, function ($query) use ($search) {
            $query->where('quizzes.title', 'like', "%$search%");
        })
        ->newQuery();    

        if ($this->course_id) {
            $quizzes->where('courses.id', $this->course_id);
        }
         return $quizzes;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:99.8%')
            ->footerCallback('function ( row, data, start, end, display ) {

                $(".dataTables_length select").addClass("form-select form-select-lg without_search mb-3");
                selectionFields();
            }')
            ->parameters([
                'dom'        => 'Blfrtip',
                'buttons'    => [
                    ['csv'],
                ],
                'lengthMenu' => [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                'language'   => [
                    'searchPlaceholder' => __('search'),
                    'lengthMenu'        => '_MENU_ '.__('course_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('category_name')->title(__('Term'))->searchable(true),
            Column::computed('course_name')->title(__('Course'))->searchable(true),
            Column::computed('quiz_name')->title(__('Quiz'))->searchable(true),
            Column::computed('quiz_submission')->title(__('Submission Date'))->searchable(true),
            Column::computed('quiz_details')->title(__('View Details'))->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'SubmittedQuizzes'.date('YmdHis');
    }
}
