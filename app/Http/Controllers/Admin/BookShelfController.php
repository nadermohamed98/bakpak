<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\AssignmentDataTable;
use App\DataTables\BookShelfDataTable;
use App\DataTables\StudentDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookShelfRequest;
use App\Models\LiveClass;
use App\Models\Resource;
use App\Repositories\BookShelfRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\LanguageRepository;
use App\Repositories\LevelRepository;
use App\Repositories\LiveClassRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\SubjectRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class BookShelfController extends Controller
{
    protected $organization;

    protected $book_shelf;

    protected $liveClass;

    protected $category;

    protected $user;

    public function __construct(OrganizationRepository $organization, BookShelfRepository $book_shelf, CategoryRepository $category, UserRepository $user, LiveClassRepository $liveclass)
    {
        $this->organization = $organization;
        $this->user         = $user;
        $this->category     = $category;
        $this->book_shelf   = $book_shelf;
        $this->liveClass    = $liveclass;
    }

    public function index(BookShelfDataTable $dataTable, Request $request, $org_id = null)
    {
        try {
            $organization  = $this->organization->find($org_id ?? $request->organization_id);

            $instructor    = $request->organization_id ? $this->user->findUsers([
                'organization_id' => $request->organization_id,
            ]) : [];

            $categories    = $request->category_ids ? $this->category->activeCategories([
                'ids'  => $request->category_ids,
                'type' => 'course',
            ]) : [];

            $data          = [
                'organization'    => $organization,
                'instructors'     => $instructor,
                'categories'      => $categories,
                'status'          => $request->status,
                'organization_id' => $request->organization_id,
                'instructor_ids'  => $request->instructor_ids,
            ];

            $filtered_data = [
                'instructor_ids' => $request->instructor_ids,
                'category_ids'   => $request->category_ids,
                'org_id'         => $org_id ?? $request->organization_id,
                'status'         => $request->status,
                'course_type'    => 'book_shelf',
            ];

            return $dataTable->with($filtered_data)->render('backend.admin.book_shelf.index', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back()->withInput();
        }
    }

    public function create(LanguageRepository $languageRepository, LevelRepository $levelRepository, TagRepository $tagRepository, SubjectRepository $subjectRepository): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $data = [
                'languages'    => $languageRepository->activeLanguage(),
                'levels'       => $levelRepository->activeLevels(),
                'tags'         => $tagRepository->activeTags(),
                'category'     => $this->category->find(old('category_id')),
                'subject'      => $subjectRepository->find(old('subject_id')),
                'organization' => $this->organization->find(old('organization_id')),
                'instructors'  => old('organization_id') ? $this->user->findUsers([
                    'organization_id' => old('organization_id'),
                ]) : [],
            ];

            return view('backend.admin.book_shelf.create', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function store(BookShelfRequest $request): \Illuminate\Http\RedirectResponse
    {
        if (config('app.demo_mode')) {
            Toastr::info(__('this_function_is_disabled_in_demo_server'));

            return back();
        }
        try {
            $course = $this->book_shelf->store($request->all());
            $this->book_shelf->published($course->id);
            Toastr::success(__('create_successful'));

            return redirect()->route('book_shelf.index');
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back()->withInput();
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id, LanguageRepository $languageRepository, LevelRepository $levelRepository, TagRepository $tagRepository, SubjectRepository $subjectRepository, AssignmentDataTable $dataTable, Request $request)
    {
        try {
            $book_shelf = $this->book_shelf->find($id);

            $data       = [
                'sections'     => $book_shelf->sections,
                'lessons'      => $book_shelf->lessons,
                'resources'    => Resource::where('course_id', $book_shelf->id)->get(),
                'faqs'         => $book_shelf->faqs,
                'languages'    => $languageRepository->activeLanguage(),
                'levels'       => $levelRepository->activeLevels(),
                'tags'         => $tagRepository->activeTags(),
                'category'     => $this->category->find(old('category_id', $book_shelf->category_id)),
                'subject'      => old('subject_id', $book_shelf->subject_id) ? $subjectRepository->find(old('subject_id', $book_shelf->subject_id)) : $subjectRepository->find(old('subject_id')),
                'organization' => $this->organization->find(old('organization_id', $book_shelf->organization_id)),
                'book_shelf'   => $book_shelf,
                'liveClass'    => LiveClass::where('course_id', $id)->first(),
                'request_tab'  => $request['tab'] ?: 'basic',
                'instructors'  => old('organization_id', $book_shelf->organization_id) ? $this->user->findUsers([
                    'organization_id' => old('organization_id', $book_shelf->organization_id),
                ]) : [],
            ];

            $this->book_shelf->published($id);

            return $dataTable->with('course_id', $id)->render('backend.admin.book_shelf.edit', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function update(BookShelfRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        if (config('app.demo_mode')) {
            Toastr::info(__('this_function_is_disabled_in_demo_server'));

            return back();
        }
        try {
            $this->book_shelf->update($request->all(), $id);

            if ($request->course_type === 'live_class') {
                $liveClass = LiveClass::where('course_id', $id)->first();
                if ($liveClass) {
                    $this->liveClass->update($request->all(), $id);
                } else {
                    $this->liveClass->store($request->all(), $id);
                }
            }

            Toastr::success(__('update_successful'));

            return redirect()->route('book_shelf.index');
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back()->withInput();
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->book_shelf->destroy($id);

            $data = [
                'status'  => 'success',
                'message' => __('delete_successful'),
                'title'   => __('success'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'status'  => 'danger',
                'message' => $e->getMessage(),
                'title'   => 'error',
            ];

            return response()->json($data);
        }
    }

    public function statusChange(Request $request): \Illuminate\Http\JsonResponse
    {
        if (config('app.demo_mode')) {
            $data = [
                'status'  => 'danger',
                'message' => __('this_function_is_disabled_in_demo_server'),
                'title'   => 'error',
            ];

            return response()->json($data);
        }
        try {
            $book_shelf             = $this->book_shelf->find($request->id);
            $request['category_id'] = $book_shelf->category_id;
            $request['title']       = $book_shelf->title;
            $this->book_shelf->update($request->all(), $request->id);
            $data                   = [
                'status'  => 'success',
                'message' => __('update_successful'),
                'title'   => 'success',
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'status'  => 'danger',
                'message' => $e->getMessage(),
                'title'   => 'error',
            ];

            return response()->json($data);
        }
    }

    public function published(Request $request): \Illuminate\Http\JsonResponse
    {
        if (config('app.demo_mode')) {
            $data = [
                'status'  => 'danger',
                'message' => __('this_function_is_disabled_in_demo_server'),
                'title'   => 'error',
            ];

            return response()->json($data);
        }
        try {
            $this->book_shelf->published($request->id);

            $data = [
                'status'  => 'success',
                'message' => __('update_successful'),
                'title'   => 'success',
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            $data = [
                'status'  => 'danger',
                'message' => $e->getMessage(),
                'title'   => 'error',
            ];

            return response()->json($data);
        }
    }

    public function students($id, StudentDataTable $dataTable)
    {
        try {
            $data = [
                'id' => $id,
            ];

            return $dataTable->with($data)->render('backend.admin.student.index', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }
}
