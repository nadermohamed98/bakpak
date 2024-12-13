<?php

namespace App\DataTables\Instructor;

use App\Models\SubmitedAssignment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StudentAssignmentsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('student_avatar', function ($assignment) {
                $user = @$assignment->student;
                return view('backend.instructor.student.image', compact('user'));

            })->addColumn('student_name', function ($assignment) {
                return @$assignment->student?->name;
            })->addColumn('assignment_title', function ($assignment) {
                return @$assignment->student_assignment?->title;
            })->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        $search = $this->request->search['value'];
        $assigments =  SubmitedAssignment::where('user_id', $this->user_id)
            ->when($this->request->search['value'] ?? false, function ($query) use ($search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('first_name','like', "%$search%")
                    ->orWhere('last_name','like', "%$search%");
                })
                ->orWhereHas('student_assignment', function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%");
                })
                ->orWhere('marks', 'like', "%$search%");
            })
            ->newQuery();
         return $assigments;
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
            Column::computed('student_avatar')->title(__('Student Avatar'))->searchable(false),
            Column::computed('student_name')->title(__('Student Name'))->searchable(false),
            Column::computed('assignment_title')->title(__('Assignment Title')),
            Column::computed('marks')->title(__('Assignment Grade'))->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'SubmittedAssignments'.date('YmdHis');
    }
}
