<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\AssignmentDataTable;
use App\DataTables\CourseDataTable;
use App\DataTables\StudentDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Faq;
use App\Models\Lesson;
use App\Models\LiveClass;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\Resource;
use App\Models\Section;
use App\Repositories\CategoryRepository;
use App\Repositories\CourseRepository;
use App\Repositories\LanguageRepository;
use App\Repositories\LevelRepository;
use App\Repositories\LiveClassRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\SubjectRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
	protected $organization;
	
	protected $course;
	
	protected $liveClass;
	
	protected $category;
	
	protected $user;
	
	public function __construct(OrganizationRepository $organization,
		CourseRepository $course,
		CategoryRepository $category,
		UserRepository $user,
		LiveClassRepository $liveclass)
	{
		$this->organization = $organization;
		$this->user = $user;
		$this->category = $category;
		$this->course = $course;
		$this->liveClass = $liveclass;
	}
	
	public function index(CourseDataTable $dataTable, Request $request, $org_id = null)
	{
		try {
			$organization = $this->organization->find($org_id ?? $request->organization_id);
			
			$instructor = $request->organization_id ? $this->user->findUsers([
				'organization_id' => $request->organization_id,
			]) : [];
			
			$categories = $request->category_ids ? $this->category->activeCategories([
				'ids' => $request->category_ids,
				'type' => 'course',
			]) : [];
			
			$book_Shelves = $this->course->getBookShelves();
			
			$data = [
				'organization' => $organization,
				'instructors' => $instructor,
				'categories' => $categories,
				'status' => $request->status,
				'organization_id' => $request->organization_id,
				'instructor_ids' => $request->instructor_ids,
				'book_shelfs' => $book_Shelves,
			];
			
			$filtered_data = [
				'instructor_ids' => $request->instructor_ids,
				'category_ids' => $request->category_ids,
				'org_id' => $org_id ?? $request->organization_id,
				'status' => $request->status,
				'course_type' => ['course', 'live_class'],
			];
			
			return $dataTable->with($filtered_data)->render('backend.admin.course.index', $data);
		} catch (\Exception $e) {
			Toastr::error($e->getMessage());
			
			return back()->withInput();
		}
	}
	
	public function create(LanguageRepository $languageRepository,
		LevelRepository $levelRepository,
		TagRepository $tagRepository,
		SubjectRepository $subjectRepository): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|RedirectResponse|\Illuminate\Contracts\Foundation\Application
	{
		try {
			$data = [
				'languages' => $languageRepository->activeLanguage(),
				'levels' => $levelRepository->activeLevels(),
				'tags' => $tagRepository->activeTags(),
				'category' => $this->category->find(old('category_id')),
				'subject' => $subjectRepository->find(old('subject_id')),
				'organization' => $this->organization->find(old('organization_id')),
				'instructors' => old('organization_id') ? $this->user->findUsers([
					'organization_id' => old('organization_id'),
				]) : [],
			];
			
			return view('backend.admin.course.create', $data);
		} catch (\Exception $e) {
			Toastr::error($e->getMessage());
			
			return back();
		}
	}
	
	public function store(CourseRequest $request): RedirectResponse
	{
		if (config('app.demo_mode')) {
			Toastr::info(__('this_function_is_disabled_in_demo_server'));
			
			return back();
		}
		try {
			$this->course->store($request->all());
			
			Toastr::success(__('create_successful'));
			
			return redirect()->route('courses.index');
		} catch (\Exception $e) {
			Toastr::error($e->getMessage());
			
			return back()->withInput();
		}
	}
	
	public function clone_from_book_shelf(Request $request): RedirectResponse
	{
		if (config('app.demo_mode')) {
			Toastr::info(__('this_function_is_disabled_in_demo_server'));
			
			return back();
		}
		try {
			$request_data = $request->all();
			$now = Carbon::now(); // Get the current timestamp
			// duplicate course row and save it as new one
			$course_data = Course::findOrfail($request_data['book_shelf_id']);
			$new_course = $course_data->replicate();
			$new_course->slug = getSlug('courses', $course_data->title);
			$new_course->status = 'draft';
			$new_course->is_published = 0;
			$new_course->course_type = 'course';
			$new_course->created_at = $now;
			$new_course->updated_at = null;
			$new_course->save();
			
			// duplicate sections data from book shelf to the new course
			$sections = Section::where('course_id', $request_data['book_shelf_id'])->get();
			foreach ($sections as $section) {
				$new_section = $section->replicate();
				$new_section->slug = getSlug('sections', $section->title);
				$new_section->course_id = $new_course->id;
				$new_section->order_no = $section->order_no;
				$new_section->created_at = $now;
				$new_section->updated_at = null;
				$new_section->save();
				
				$lessons = Lesson::where('course_id', $request_data['book_shelf_id'])->where('section_id',
					$section->id)->get();
				foreach ($lessons as $lesson) {
					$new_lesson = $lesson->replicate();
					$new_lesson->slug = getSlug('lessons', $lesson->title);
					$new_lesson->course_id = $new_course->id;
					$new_lesson->section_id = $new_section->id;
					$new_lesson->created_at = $now;
					$new_lesson->updated_at = null;
					$new_lesson->save();
					
					$assignments = Assignment::where('course_id', $request_data['book_shelf_id'])
						->where('section_id', $section->id)
						->where('lesson_id', $lesson->id)
						->get();
					foreach ($assignments as $assignment) {
						$new_assignment = $assignment->replicate();
						$new_assignment->slug = getSlug('assignments', $assignment->title);
						$new_assignment->course_id = $new_course->id;
						$new_assignment->section_id = $new_section->id;
						$new_assignment->lesson_id = $new_lesson->id;
						$new_assignment->created_at = $now;
						$new_assignment->updated_at = null;
						$new_assignment->save();
					}
				}
				
				$quizzes = Quiz::where('section_id', $section->id)->get();
				foreach ($quizzes as $quizze) {
					$new_quizze = $quizze->replicate();
					$new_quizze->slug = getSlug('quizzes', $quizze->title);
					$new_quizze->section_id = $new_section->id;
					$new_quizze->created_at = $now;
					$new_quizze->updated_at = null;
					$new_quizze->save();
					
					$questions = QuizQuestion::where('quiz_id', $quizze->id)->get();
					foreach ($questions as $question) {
						$new_question = $question->replicate();
						$new_question->quiz_id = $new_quizze->id;
						$new_question->created_at = $now;
						$new_question->updated_at = null;
						$new_question->save();
						
						$question_answers = QuizAnswer::where('quiz_id', $quizze->id)->where('quiz_question_id',
							$question->id)->get();
						foreach ($question_answers as $question_answer) {
							$new_question_answer = $question_answer->replicate();
							$new_question_answer->quiz_id = $new_quizze->id;
							$new_question_answer->quiz_question_id = $new_question->id;
							$new_question_answer->created_at = $now;
							$new_question_answer->updated_at = null;
							$new_question_answer->save();
						}
					}
				}
			}
			
			// duplicate Resources data from bookshelf to the new course
			$resources = Resource::where('course_id', $request_data['book_shelf_id'])->get();
			foreach ($resources as $resource) {
				$new_resource = $resource->replicate();
				$new_resource->slug = getSlug('resources', $resource->title);
				$new_resource->course_id = $new_course->id;
				$new_resource->section_id = $new_section->id;
				$new_resource->created_at = $now;
				$new_resource->updated_at = null;
				$new_resource->save();
			}
			
			// duplicate faqs data from bookshelf to the new course
			$faqs = Faq::where('course_id', $request_data['book_shelf_id'])->get();
			foreach ($faqs as $faq) {
				$new_faq = $faq->replicate();
				$new_faq->course_id = $new_course->id;
				$new_faq->save();
			}
			
			// duplicate live_classes data from bookshelf to the new course
			$live_classes = LiveClass::where('course_id', $request_data['book_shelf_id'])->get();
			foreach ($live_classes as $live_class) {
				$new_live_class = $live_class->replicate();
				$new_live_class->slug = getSlug('live_classes', $live_class->title);
				$new_live_class->course_id = $new_course->id;
				$new_live_class->created_at = $now;
				$new_live_class->updated_at = null;
				$new_live_class->save();
			}
			
			Toastr::success(__('create_successful'));
			
			return redirect()->back();
		} catch (\Exception $e) {
			Toastr::error($e->getMessage());
			
			return back()->withInput();
		}
	}
	
	public function show($id)
	{
		//
	}
	
	public function edit($id,
		LanguageRepository $languageRepository,
		LevelRepository $levelRepository,
		TagRepository $tagRepository,
		SubjectRepository $subjectRepository,
		AssignmentDataTable $dataTable,
		Request $request)
	{
		try {
			$course = $this->course->find($id);
			
			$data = [
				'sections' => $course->sections,
				'lessons' => $course->lessons,
				'resources' => Resource::where('course_id', $course->id)->get(),
				'faqs' => $course->faqs,
				'languages' => $languageRepository->activeLanguage(),
				'levels' => $levelRepository->activeLevels(),
				'tags' => $tagRepository->activeTags(),
				'category' => $this->category->find(old('category_id', $course->category_id)),
				'subject' => old('subject_id', $course->subject_id) ? $subjectRepository->find(old('subject_id',
					$course->subject_id)) : $subjectRepository->find(old('subject_id')),
				'organization' => $this->organization->find(old('organization_id', $course->organization_id)),
				'course' => $course,
				'liveClass' => LiveClass::where('course_id', $id)->first(),
				'request_tab' => $request['tab'] ?: 'basic',
				'instructors' => old('organization_id', $course->organization_id) ? $this->user->findUsers([
					'organization_id' => old('organization_id', $course->organization_id),
				]) : [],
			];
			
			return $dataTable->with('course_id', $id)->render('backend.admin.course.edit', $data);
		} catch (\Exception $e) {
			Toastr::error($e->getMessage());
			
			return back();
		}
	}
	
	public function update(CourseRequest $request, $id): RedirectResponse
	{
		if (config('app.demo_mode')) {
			Toastr::info(__('this_function_is_disabled_in_demo_server'));
			
			return back();
		}
		try {
			$this->course->update($request->all(), $id);
			
			if ($request->course_type == 'live_class') {
				$liveClass = LiveClass::where('course_id', $id)->first();
				if ($liveClass) {
					$this->liveClass->update($request->all(), $id);
				} else {
					$this->liveClass->store($request->all(), $id);
				}
			}
			
			Toastr::success(__('update_successful'));
			
			return redirect()->route('courses.index');
		} catch (\Exception $e) {
			Toastr::error($e->getMessage());
			
			return back()->withInput();
		}
	}
	
	public function destroy($id): \Illuminate\Http\JsonResponse
	{
		try {
			$this->course->destroy($id);
			$data = [
				'status' => 'success',
				'message' => __('delete_successful'),
				'title' => __('success'),
			];
			
			return response()->json($data);
		} catch (\Exception $e) {
			$data = [
				'status' => 'danger',
				'message' => $e->getMessage(),
				'title' => 'error',
			];
			
			return response()->json($data);
		}
	}
	
	public function statusChange(Request $request): \Illuminate\Http\JsonResponse
	{
		if (config('app.demo_mode')) {
			$data = [
				'status' => 'danger',
				'message' => __('this_function_is_disabled_in_demo_server'),
				'title' => 'error',
			];
			
			return response()->json($data);
		}
		try {
			$course = $this->course->find($request->id);
			$request['category_id'] = $course->category_id;
			$request['title'] = $course->title;
			$this->course->update($request->all(), $request->id);
			$data = [
				'status' => 'success',
				'message' => __('update_successful'),
				'title' => 'success',
			];
			
			return response()->json($data);
		} catch (\Exception $e) {
			$data = [
				'status' => 'danger',
				'message' => $e->getMessage(),
				'title' => 'error',
			];
			
			return response()->json($data);
		}
	}
	
	public function published(Request $request): \Illuminate\Http\JsonResponse
	{
		if (config('app.demo_mode')) {
			$data = [
				'status' => 'danger',
				'message' => __('this_function_is_disabled_in_demo_server'),
				'title' => 'error',
			];
			
			return response()->json($data);
		}
		try {
			$this->course->published($request->id);
			
			$data = [
				'status' => 'success',
				'message' => __('update_successful'),
				'title' => 'success',
			];
			
			return response()->json($data);
		} catch (\Exception $e) {
			$data = [
				'status' => 'danger',
				'message' => $e->getMessage(),
				'title' => 'error',
			];
			
			return response()->json($data);
		}
	}
	
	public function students($id, StudentDataTable $dataTable)
	{
		try {
			$course = $this->course->find($id);
			$data = [
				'id' => $id,
				'course' => $course
			];
			
			return $dataTable->with($data)->render('backend.admin.student.index', $data);
		} catch (\Exception $e) {
			Toastr::error($e->getMessage());
			
			return back();
		}
	}
}