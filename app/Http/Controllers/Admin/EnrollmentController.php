<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\EnrollmentDataTable;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enroll;
use App\Models\User;
use App\Repositories\CheckoutRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public $checkoutRepository;

    public function __construct(CheckoutRepository $checkoutRepository)
    {
        $this->checkoutRepository = $checkoutRepository;
    }

    public function index(EnrollmentDataTable $dataTable)
    {
        $courses = Course::all();
        $students = User::where('role_id', 3)->get();
        $categories = Category::all();

        return $dataTable->render('backend.admin.enrollment.index', compact('courses', 'students', 'categories'));
    }


    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required',
            'course_id'  => 'required',
        ]);

        try {
            DB::beginTransaction();
            $this->checkoutRepository->bulkEnrolls($request->all());
            DB::commit();

            return response()->json([
                'success' => __('enrolled_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function statusChange($id): \Illuminate\Http\JsonResponse
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
            $status = $this->checkoutRepository->changeStatus($id);

            $data   = [
                'status'  => 'success',
                'message' => $status ? __('enrollment_approved_successfully') : __('enrollment_rejected_successfully'),
                'title'   => __('success'),
            ];

            return response()->json($data);
        } catch (\Exception $e) {

            $data = [
                'status'  => 400,
                'message' => $e->getMessage(),
                'title'   => 'error',
            ];

            return response()->json($data);
        }
    }

    public function destroy($id)
    {
        // Find the enrollment record
        $enroll = Enroll::find($id);
        if(!$enroll) {
            return response()->json(['error' => 'Enrollment not found']);
        }
        // Delete the record
        $enroll->delete();
        // Return a success response
        return response()->json(['success' => 'Enrollment removed successfully']);
    }
}
