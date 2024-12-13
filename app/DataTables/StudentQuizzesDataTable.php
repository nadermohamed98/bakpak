<?php

namespace App\DataTables;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StudentQuizzesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        \Log::Info("DataTable Query",  $query);
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('quiz_name', function ($quiz) {
                return $quiz->title; // Display the quiz title
            })
            ->addColumn('course_name', function ($quiz) {
                return $quiz->course_title; // Display the course title from the join
            })
            ->addColumn('section_name', function ($quiz) {
                return $quiz->section_title; // Display the section title from the join
            })
            ->addColumn('quiz_details', function ($quiz) {
                $quizSlug = encrypt($quiz->id);
                $userId = $this->request()->get('user_id');           
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
        $userId = $this->request()->get('user_id');  // Retrieve the user_id passed in
    
        \Log::info('User ID for filtering quizzes:', [$userId]); // Log the user ID
    
        $quizzes = Quiz::select('quizzes.*', 'courses.title as course_title', 'sections.title as section_title')
            ->join('sections', 'sections.id', '=', 'quizzes.section_id')
            ->join('courses', 'courses.id', '=', 'sections.course_id')
            ->whereExists(function ($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('quiz_questions')
                    ->whereColumn('quiz_questions.quiz_id', 'quizzes.id')
                    ->whereExists(function ($subQuery) use ($userId) {
                        $subQuery->select(DB::raw(1))
                            ->from('quiz_answers')
                            ->whereColumn('quiz_answers.quiz_question_id', 'quiz_questions.id')
                            ->where('quiz_answers.user_id', $userId);  // Filter by user_id
                    });
            });
    
        // Log the final query
        \Log::info('Executed query:', [$quizzes->toSql(), $quizzes->getBindings()]);
    
        // Add this to inspect the data being fetched
        dd($quizzes->get());
    
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
            Column::computed('quiz_name')->title(__('Quiz Name'))->searchable(true),
            Column::computed('course_name')->title(__('Course Name'))->searchable(true),
            Column::computed('section_name')->title(__('Section Name'))->searchable(true),
            Column::computed('quiz_details')->title(__('View Details'))->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'SubmittedQuizzes'.date('YmdHis');
    }
}
