<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ActivityLogDataTable;
use App\DataTables\CheckoutDataTable;
use App\DataTables\InstructorDataTable;
use App\DataTables\StudentDataTable;
use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Repositories\CertificateRepository;
use App\Repositories\CheckoutRepository;
use App\Repositories\CityRepository;
use App\Repositories\CountryRepository;
use App\Repositories\CourseRepository;
use App\Repositories\StateRepository;
use App\Repositories\StudentRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Matrix\Exception;
use Log;

class StudentController extends Controller
{
    protected $student;
    public $checkoutRepository;

    public function __construct(StudentRepository $student,CheckoutRepository $checkoutRepository)
    {
        $this->student = $student;
        $this->checkoutRepository = $checkoutRepository;
    }

    public function index(StudentDataTable $dataTable,$courseId = null)
    {
        try {
            $data = [
                'id' => $courseId,
            ];

            return $dataTable->render('backend.admin.student.index',$data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function create(CountryRepository $countryRepository,Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $data = [
                'countries' => $countryRepository->activeCountries(),
                'courseId'  => $request['courseId'],
            ];

            return view('backend.admin.student.create', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        if (config('app.demo_mode')) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }
        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6|confirmed',
            'phone'      => 'required|unique:users,phone',
        ]);
        DB::beginTransaction();
        try {
            $student = $this->student->store($request->all());
            Toastr::success(__('create_successful'));

            if ($request['courseId'] != null ){
                $student_course = [
                    'student_id' => [ $student['id'] ],
                    'course_id'  => [ $request['courseId'] ],
                ];

                $this->checkoutRepository->bulkEnrolls($student_course);
                DB::commit();
                return response()->json([
                    'success' => __('create_successful'),
                    'route'   => route('course.students',$request['courseId']),
                ]);
            }else{
                return response()->json([
                    'success' => __('create_successful'),
                    'route'   => route('students.index'),
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function createExcel(Request $request): \Illuminate\Http\JsonResponse
    {
        if (config('app.demo_mode')) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }
        $request->validate([
            'file_excel' => 'required|mimes:xls,xlsx'
        ]);

        DB::beginTransaction();
        try {
            $import = new StudentsImport();
            Excel::import($import, $request->file('file_excel'));

            if ($request['courseId'] != null ) {
                foreach ($import->importedStudents as $student) {
                    $student_course = [
                        'student_id' => [ $student['id'] ],
                        'course_id'  => [ $request['courseId'] ],
                    ];

                    $this->checkoutRepository->bulkEnrolls($student_course);
                }
            }

            DB::commit();
            Toastr::success(__('added_successful'));

            return response()->json([
                'success' => __('added_successful'),
                'route'   => route('students.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }


    public function show($id, CourseRepository $repository): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $user    = $this->student->find($id);
            $courses = $repository->activeCourses([
                'user_id'   => $id,
                'paginate'  => setting('paginate'),
                'my_course' => 1,
            ]);

            $data    = [
                'user'    => $user,
                'courses' => $courses,
            ];

            return view('backend.admin.student.profile', $data);

        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function loadCourses(Request $request, CourseRepository $repository): \Illuminate\Http\JsonResponse
    {
        try {
            $courses = $repository->activeCourses([
                'user_id'   => $request->id,
                'paginate'  => setting('paginate'),
                'my_course' => 1,
            ]);

            return response()->json([
                'success'       => true,
                'next_page_url' => $courses->nextPageUrl(),
                'courses'       => view('backend.admin.course.component_with_progress', compact('courses'))->render(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function certificates($id, CertificateRepository $certificateRepository, CourseRepository $courseRepository): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {

        try {
            $user    = $this->student->find($id);
            $courses = $courseRepository->activeCourses([
                'my_course'   => 1,
                'user_id'     => $user->id,
                'paginate'    => setting('paginate'),
                'course_view' => setting('course_view_percent'),
            ], ['enrolls']);
            $data    = [
                'user'    => $user,
                'courses' => $courses,
            ];

            return view('backend.admin.student.profile', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function loadCertificates(Request $request, CertificateRepository $certificateRepository, CourseRepository $courseRepository): \Illuminate\Http\JsonResponse
    {
        try {
            $courses = $courseRepository->activeCourses([
                'my_course'   => 1,
                'user_id'     => $request->id,
                'paginate'    => setting('paginate'),
                'course_view' => setting('course_view_percent'),
            ], ['enrolls']);

            return response()->json([
                'success'       => true,
                'next_page_url' => $courses->nextPageUrl(),
                'certificates'  => view('backend.admin.certificate.component', compact('courses'))->render(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function instructors($id, InstructorDataTable $dataTable)
    {
        try {
            $user = $this->student->find($id);
            $data = [
                'user' => $user,
            ];

            return $dataTable->with([
                'user_id'   => $id,
                'following' => 1,
            ])->render('backend.admin.student.profile', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function payments($id, CheckoutDataTable $dataTable)
    {
        try {
            $user = $this->student->find($id);

            $data = [
                'user' => $user,
            ];

            return $dataTable->with([
                'user_id' => $id,
                'payment' => 1,
            ])->render('backend.admin.student.profile', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function logs($id, ActivityLogDataTable $dataTable)
    {
        try {
            $user = $this->student->find($id);

            $data = [
                'user' => $user,
            ];

            return $dataTable->with([
                'user_id' => $id,
            ])->render('backend.admin.student.profile', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function edit($id, CountryRepository $repository, StateRepository $stateRepository, CityRepository $cityRepository): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $user = $this->student->find($id);
            $data = [
                'countries' => $repository->activeCountries(),
                'states'    => $stateRepository->stateByCountry($user->country_id),
                'cities'    => $cityRepository->cityByState($user->state_id),
                'user'      => $user,
            ];

            return view('backend.admin.student.edit', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        if (config('app.demo_mode')) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }
        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users,email,'.$id,
            'phone'      => 'required|unique:users,email,'.$id,
        ]);
        DB::beginTransaction();
        try {
            $this->student->update($request->all(), $id);
            DB::commit();
            Toastr::success(__('update_successful'));

            return response()->json([
                'success' => __('update_successful'),
                'route'   => route('students.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function certificateDownload($id, CertificateRepository $certificateRepository)
    {

        try {
            $certificate = $certificateRepository->findCertificate($id);
            if ($certificate) {
                $pdf      = Pdf::loadView('backend.admin.certificate.download_certificate', compact('certificate'));

                $pdf_name = $certificate->title.'.pdf';

                return $pdf->download($pdf_name);
            } else {
                Toastr::warning(__('certificate_not_found'));

                return redirect()->back();
            }

        } catch (Exception $e) {
            Toastr::warning($e->getMessage());

            return redirect()->back();
        }

    }
}
