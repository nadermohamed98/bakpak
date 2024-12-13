<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Book;
use App\Models\Course;
use App\Models\LiveClass;
use App\Models\Meeting;
use App\Models\QuizAnswer;
use App\Models\Resource;
use App\Models\SubmitedAssignment;
use App\Models\Wishlist;
use App\Repositories\AssignmentRepository;
use App\Repositories\BookRepository;
use App\Repositories\CertificateRepository;
use App\Repositories\CourseRepository;
use App\Repositories\LiveClassRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OfflineMethodRepository;
use App\Models\Quiz;
use App\Repositories\QuizRepository;
use App\Repositories\SettingRepository;
use App\Repositories\SubmitedAssignmentRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Traits\PaymentTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfileController extends Controller
{
    use PaymentTrait;

    protected $course;

    protected $book;

    protected $user;

    public function __construct(CourseRepository $course, BookRepository $book, UserRepository $user)
    {
        $this->course = $course;
        $this->book   = $book;
        $this->user   = $user;
    }

    public function myProfile()
    {

        // dd(session()->get('carts'));
        try {
            $purchased_course = $this->course->activeCourses([
                'my_course' => 1,
                'user_id'   => Auth::user()->id,
                'paginate'  => 5,
            ]);

            $courseIds = [];
            foreach($purchased_course as $course) {
                $courseIds[] = $course->id;
            }


           // $courses = $purchased_course->get();
            $my_assignments             = Assignment::where('status', 1)->CourseAssignment()->pluck('id');
            $submited_assignment        = SubmitedAssignment::whereIn('assignment_id', $my_assignments)->count();
            $due_assignments            = $my_assignments->count() - $submited_assignment;
            $meeting = LiveClass::whereIn('course_id', $courseIds)
            ->where('end_at', '>', date('Y-m-d h:i:s')) 
            ->with('course.user')
            ->get();

            \Log::Info("CourseIds ", [$meeting]);
            
            $data             = [
                'purchased_course' => $purchased_course,
                'purchased_book'   => 0,
                'course_wishlist'  => Wishlist::where('user_id', auth()->id())->where('wishable_type', Course::class)->count(),
                'book_wishlist'    => addon_is_activated('book_store') ? Wishlist::where('user_id', auth()->id())->where('wishable_type', Book::class)->count() : 0,
                'assignment'       => $due_assignments,
                'balance'          => auth()->user()->balance,
                'total_meeting'    => $meeting->count()
            ];

            return view('frontend.profile.profile_dashboard', $data);
        } catch (Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function purchaseCourses(Request $request): View|Factory|JsonResponse|RedirectResponse|Application
    {
        try {
            $courses = $this->course->activeCourses([
                'my_course' => 1,
                'user_id'   => auth()->id(),
                'paginate'  => setting('paginate'),
            ], ['enrolls']);
            if ($courses->previousPageUrl()) {
                if ($courses->onLastPage()) {
                    $total_results = $courses->total();
                } else {
                    $total_results = $request->page * $courses->perPage();
                }
            }

            if ($courses->onFirstPage()) {
                $total_results = $courses->count();
            }

            if (request()->ajax()) {
                $course_view = '';
                foreach ($courses as $key => $course) {
                    $vars = [
                        'course' => $course,
                        'key'    => $key,
                    ];
                    $course_view .= view('frontend.profile.components.purchase_course', $vars)->render();
                }

                return response()->json([
                    'success'       => true,
                    'html'          => $course_view,
                    'next_page'     => $courses->nextPageUrl(),
                    'total_results' => $total_results ?? 0,
                    'total_courses' => $courses->total(),
                ]);
            }

            $data    = [
                'courses'       => $courses,
                'total_courses' => $courses->total(),
                'total_results' => $total_results ?? 0,
            ];

            return view('frontend.profile.purchase_courses', $data);
        } catch (Exception $e) {
            if (\request()->ajax()) {
                return response()->json([
                    'error' => $e->getMessage(),
                ]);
            } else {
                Toastr::error($e->getMessage());

                return back();
            }
        }
    }

    /* public function recentlyViewedCourses(RecentViewRepository $recent_view_repository)
    {
         $recent_view_courses = $recent_view_repository->all([
            'user_id'        => auth()->user()->id,
            'type'           => 'course'
        ]);

         return view('frontend.profile.recently_viewed_courses', compact('recent_view_courses'));
     }*/

    public function purchaseBook()
    {
        return view('frontend.profile.purchase_book');
    }

    public function recentlyViewedBook()
    {
        return view('frontend.profile.recently_viewed_book');
    }

    public function wishlistBook()
    {
        return view('frontend.profile.wishlist_book');
    }

    public function notification(NotificationRepository $notification)
    {
        $notifications       = $notification->all([
            'user_id'  => auth()->user()->id,
            'paginate' => setting('paginate'),
        ]);

        $total_notifications = $notification->UserNotification([
            'user_id' => auth()->user()->id,
        ])->count();
        $data                = [
            'notifications'       => $notifications,
            'total_notifications' => $total_notifications,
        ];

        return view('frontend.profile.notification', $data);
    }

    public function notificationUpdate(Request $request, NotificationRepository $notification)
    {
        $selected_ids    = $request->selected_id;
        $data            = [];
        $data['is_read'] = $request->update_type;

        try {
            $notification->update($data, $selected_ids);
            $response = [
                'status'  => 200,
                'title'   => 'success',
                'message' => __('update_successful'),
            ];

            return response()->json($response);
        } catch (Exception $e) {
            $response = [
                'status'  => 400,
                'title'   => 'error',
                'message' => __($e->getMessage()),
            ];

            return response()->json($response);
        }
    }

    public function notificationDelete(Request $request, NotificationRepository $notification): JsonResponse|RedirectResponse
    {
        $selected_ids = $request->selected_id;

        if (! $selected_ids) {
            $response = [
                'status'  => 400,
                'title'   => 'error',
                'message' => __('item_not_found'),
            ];

            return response()->json($response);
        }
        $data         = [];
        try {
            $notification->delete($selected_ids);
            $response = [
                'status'  => 200,
                'title'   => 'success',
                'message' => __('delete_successful'),
            ];

            return response()->json($response);
        } catch (Exception $e) {
            $response = [
                'status'  => 400,
                'title'   => 'error',
                'message' => __($e->getMessage()),
            ];

            return response()->json($response);
        }
    }

    public function profileSetting()
    {
        return view('frontend.profile.setting');
    }

    public function systemStatus(Request $request, SettingRepository $setting): JsonResponse|\Illuminate\Routing\Redirector|RedirectResponse|Application
    {
        if (config('app.demo_mode')) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }
        try {
            if (array_key_exists('maintenance_secret', $request->all())) {
                $command = $request['maintenance_secret'];
                if ($setting->update($request)) {
                    Artisan::call('down --refresh=15 --secret='.$command);
                    Toastr::success(__('Updated Successfully'));

                    return redirect('/'.$command);
                } else {
                    Toastr::error(__('Something went wrong, please try again'));

                    return back();
                }
            }
            if (config('app.demo_mode')) {
                $response['message'] = __('This function is disabled in demo server.');
                $response['title']   = __('Ops..!');
                $response['status']  = 'error';

                return response()->json($response);
            }
            if ($setting->statusChange($request->data)) {
                if ($request['data']['name'] == 'maintenance_mode') {
                    Artisan::call('up');
                }

                if ($request['data']['name'] == 'migrate_web') {
                    if (is_dir('resources/views/admin/store-front')) {
                        envWrite('MOBILE_MODE', 'off');
                        Artisan::call('optimize:clear');
                    } else {
                        $response['message'] = __('migrate_permission');
                        $response['title']   = __('error');
                        $response['status']  = 'error';
                        $response['type']    = 'migrate_error';

                        return response()->json($response);
                    }
                }

                $response['message'] = __('Updated Successfully');
                $response['title']   = __('Success');
                $response['status']  = 'success';
            } else {
                $response['message'] = __('Something went wrong, please try again');
                $response['title']   = __('Ops..!');
                $response['status']  = 'error';
            }

            return response()->json($response);
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
            $response['title']   = __('Ops..!');
            $response['status']  = 'error';

            return response()->json($response);
        }
    }

    public function accountDelete(Request $request, UserRepository $user): JsonResponse
    {
        if (config('app.demo_mode')) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        $user_id     = $request->id;
        $logged_user = $user->find($user_id);
        if ($logged_user->is_deleted == 0) {
            try {
                $user->userDelete($user_id);
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                Toastr::success(__('delete_successful'));
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
                    'title'   => __('error'),
                ];

                return response()->json($data);
            }
        } else {
            $data = [
                'status'  => 'danger',
                'message' => __('you_account_has_been_deleted'),
                'title'   => __('error'),
            ];

            return response()->json($data);
        }
    }

    public function editProfile()
    {
        return view('frontend.profile.edit_profile');
    }

    public function wallet(WalletRepository $wallet, Request $request, OfflineMethodRepository $offlineMethodRepository): View|Factory|JsonResponse|RedirectResponse|Application
    {
        if (! setting('wallet_system')) {
            abort(404);
        }
        try {
            $wallets = $wallet->all([
                'paginate' => 5,
                'user_id'  => \auth()->id(),
            ]);

            if (request()->ajax()) {
                $view = '';
                foreach ($wallets as $key => $wallet) {
                    $view_vars = [
                        'key'     => $key,
                        'wallet'  => $wallet,
                        'wallets' => $wallets,
                    ];
                    $view .= view('frontend.profile.components.wallet', $view_vars)->render();
                }

                return response()->json([
                    'html'      => $view,
                    'next_page' => $wallets->nextPageUrl(),
                ]);
            }

            $image   = 'https://lms.spagreen.net/public/frontend/img/logo.png';
            if (setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image'])) {
                $image = get_media(setting('dark_logo')['original_image']);
            } elseif (setting('light_logo') && @is_file_exists(setting('light_logo')['original_image'])) {
                $image = get_media(setting('light_logo')['original_image']);
            }

            $data    = [
                'wallets'         => $wallets,
                'token'           => $request->token,
                'trx_id'          => Str::random(12),
                'amount'          => 0,
                'gh_price'        => 0,
                'image'           => $image,
                'post_data'       => $this->jazzCashPayment(),
                'offline_methods' => $offlineMethodRepository->activeMethods(),
            ];

            return view('frontend.profile.my_wallet', $data);
        } catch (Exception $e) {
            Toastr::error($e->getMessage());

            return back();
        }
    }

    public function walletRecharge(Request $request, WalletRepository $wallet): JsonResponse|RedirectResponse
    {
        if (! setting('wallet_system')) {
            abort(404);
        }
        try {
            $inputs = $request->all();

            if ($request->opt_b) {
                $inputs['user_id']      = $request->opt_d;
                $inputs['trx_id']       = $request->opt_b;
                $inputs['payment_type'] = 'aamarpay';
            } else {
                $inputs['user_id']      = auth()->id();
                $inputs['trx_id']       = $request->trx_id;
                $inputs['payment_type'] = $request->payment_type;
            }

            $data   = [
                'success'   => 'Recharge Successful',
                'wallet'    => $wallet->walletRecharge($inputs),
                'url'       => route('wallet'),
                'route'     => route('wallet'),
                'is_reload' => true,
            ];
            if ($request->token) {
                return redirect()->route('api.payment.success');
            } elseif ($request->ajax()) {
                return response()->json($data);
            } else {
                Toastr::success($data['success']);

                return redirect()->route('wallet');
            }
        } catch (Exception $e) {
            dd($e);
            if ($request->token) {
                return redirect()->route('api.payment.error');
            } elseif ($request->ajax()) {
                return response()->json([
                    'error' => $e->getMessage(),
                ]);
            } else {
                Toastr::error($e->getMessage());

                return redirect()->route('wallet');
            }
        }
    }

    public function meeting(LiveClassRepository $liveClassRepository)
    {
        $courses    = $this->course->activeCoursesIDs([
            'my_course' => 1,
            'user_id'   => auth()->id(),
            'paginate'  => setting('paginate'),
        ], ['enrolls']);

        $my_meeting = $liveClassRepository->myMeeting($courses);
        $data       = [
            'my_meeting' => $my_meeting,
        ];

        return view('frontend.profile.meeting', $data);
    }

    public function updateProfile(Request $request)
    {
        $validate = $request->validate([
            'first_name' => 'required|string',
            'phone'      => 'required|numeric|unique:users,phone,'.Request()->user_id,
            'email'      => 'required|email|unique:users,email,'.Request()->user_id,
            'image'      => 'mimes:jpg,JPG,JPEG,jpeg,png,PNG,webp,WEBP|max:5120',
        ]);

        $id       = Auth::user()->id;
        try {
            $this->user->update($request->all(), $id);
            Toastr::success(__('update_successful'));

            return redirect()->back();
        } catch (Exception $e) {
            Toastr::warning(__($e->getMessage()));

            return redirect()->back();
        }
    }

    public function myQuizzes(Request $request)
    {
        $userId =  $userId = $request->input('user_id') ?? Auth::user()->id;

        //TODO: I need to work on making this query preform better.
        $quizzes = Quiz::select(
            'quizzes.*',
            'courses.title as course_title',
            'sections.title as section_title',
            'categories.title as category_title',
            'quiz_answers.updated_at as quiz_submission')
            ->join('sections', 'sections.id', '=', 'quizzes.section_id')
            ->join('courses', 'courses.id', '=', 'sections.course_id')
            ->join('categories', 'categories.id', '=', 'courses.category_id')
            ->leftJoin('quiz_questions', 'quiz_questions.quiz_id', '=', 'quizzes.id') // Join quiz_questions
            ->leftJoin('quiz_answers', function ($join) use ($userId) {
                $join->on('quiz_answers.quiz_question_id', '=', 'quiz_questions.id')
                     ->where('quiz_answers.user_id', '=', $userId); // Join quiz_answers with user condition
            })
            ->whereExists(function ($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('quiz_questions')
                    ->whereColumn('quiz_questions.quiz_id', 'quizzes.id')
                    ->whereExists(function ($subQuery) use ($userId) {
                        $subQuery->select(DB::raw(1))
                            ->from('quiz_answers')
                            ->whereColumn('quiz_answers.quiz_question_id', 'quiz_questions.id')
                            ->where('quiz_answers.user_id', $userId);
                    });
            })
            ->orderby('quiz_submission', 'desc')
            ->groupBy('quizzes.id', 'courses.title', 'sections.title', 'categories.title')
            ->get();
           
            $totalQuizzes = 0;
            $passedQuizzes = 0; 
            $failedQuizzes = 0; 
            
            $quizData = [];
            foreach ($quizzes as $quiz) {
                $totalQuizzes++;
                $question_mark = ($quiz->total_marks / $quiz->questions->count());
                $correct_answer = QuizAnswer::where([
                    ['user_id', $userId],
                    ['quiz_id', $quiz->id],
                ])
                ->whereRaw(DB::raw('answers = correct_answer'))
                ->where('answers', '!=', null)
                ->count();
            
                $found_mark = ($correct_answer * $question_mark);
            
                if ($found_mark >= $quiz->pass_marks) {
                    $passedQuizzes++;  
                } else {
                    $failedQuizzes++;  
                }

                $quizData[] = [
                    'id' => $quiz->id,
                    'slug' => $quiz->slug,
                    'status' => $quiz->status,
                    'title' => $quiz->title,
                    'category_title' => $quiz->category_title,
                    'course_title' => $quiz->course_title,
                    'quiz_submission' => $quiz->quiz_submission,
                    'pass_marks' => $quiz->pass_marks,
                    'total_marks' => $quiz->total_marks,
                    'questions' => [
                        'count' => $quiz->questions->count()
                    ],
                    'found_mark' => $found_mark
                ];
            }
           
            $quizDataCollection = collect($quizData);  
            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            $perPage = setting('paginate');
            
            $currentPageResults = $quizDataCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $result = new LengthAwarePaginator(
                $currentPageResults,   // The items for the current page
                $quizDataCollection->count(),  // Total number of items
                $perPage,              // Items per page
                $currentPage,          // Current page number
                ['path' => LengthAwarePaginator::resolveCurrentPath()]  // Keep the current path for pagination links
            );
            
            if ($request->ajax()) {
                return response()->json([
                    'data' => $result->items(),  
                    'total_quizzes' => $totalQuizzes,       
                    'passed_quizzes' => $passedQuizzes,     
                    'failed_quizzes' => $failedQuizzes,    
                    'pagination' => [
                        'total' => $result->total(), // Total number of items
                        'per_page' => $result->perPage(),  // Items per page
                        'current_page' => $result->currentPage(),  // Current page
                        'last_page' => $result->lastPage(),  // Last page number
                        'next_page_url' => $result->nextPageUrl(),  // URL for next page
                        'prev_page_url' => $result->previousPageUrl(),  // URL for previous page
                    ]
                ]);
            }            

        return view('frontend.profile.my_quizzes', ['quizzes' => $result->items()]);
    }
    

    public function myAssignment(Request $request, CourseRepository $course)
    {
        $assignments = Assignment::where('status', 1)->CourseAssignment()->paginate(setting('paginate'));

        if ($assignments->previousPageUrl()) {
            $input['total_results'] = $request->page * $assignments->perPage();
        }

        if ($assignments->onFirstPage()) {
            $input['total_results'] = $assignments->count();
        }
        $input['total_assignments'] = $assignments->total();
        if (request()->ajax()) {
            return response()->json($this->ajaxFilter($assignments, $input));
        }

        //$due_assignments = Assignment::where('status', 1)->CourseAssignment()->count();
        $my_assignments             = Assignment::where('status', 1)->CourseAssignment()->pluck('id');
        $submited_assignment        = SubmitedAssignment::whereIn('assignment_id', $my_assignments)->count();
        $due_assignments            = $my_assignments->count() - $submited_assignment;
        $complete_assignment        = SubmitedAssignment::whereIn('assignment_id', $my_assignments)->where('status', 1)->count();
        $failed_assignment          = SubmitedAssignment::whereIn('assignment_id', $my_assignments)->where('status', 2)->count();

        $data                       = [
            'assignments'         => $assignments,
            'due_assignments'     => $due_assignments,
            'complete_assignment' => $complete_assignment,
            'failed_assignment'   => $failed_assignment,
        ];

        return view('frontend.profile.my_assignment', $data);
    }


    protected function ajaxFilter($assignments, $input): array
    {
        try {
            $assignment_view = '';
            foreach ($assignments as $key => $assignment) {
                $vars = [
                    'assignment' => $assignment,
                    'key'        => $key,
                ];

                $assignment_view .= view('frontend.profile.my_assignment_load_more', $vars)->render();
            }

            return [
                'assignments' => $assignment_view,
                'next_page'   => $assignments->nextPageUrl(),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function assignmentDetails($slug, AssignmentRepository $assignmentRepository)
    {
        $assignment           = Assignment::where('slug', $slug)->first();
        $submitted_assignment = SubmitedAssignment::where('user_id', auth()->user()->id)->where('assignment_id', $assignment->id)->first();
        $data                 = [
            'assignment'           => $assignment,
            'submitted_assignment' => $submitted_assignment,
        ];

        return view('frontend.profile.submit_assignment', $data);
    }

    public function assignmentSubmit(Request $request, SubmitedAssignmentRepository $submitedAssignmentRepository)
    {

        $request->validate([
            'submitted_file' => 'required|mimes:pdf,zip',
        ]);
        try {
            $submitted_assignment = SubmitedAssignment::where('user_id', auth()->user()->id)->where('assignment_id', $request->assignment_id)->first();

            if (blank($submitted_assignment)) {
                $request['user_id'] = auth()->user()->id;
                $submitedAssignmentRepository->store($request->all());
                Toastr::success(__('submitted_successful'));

                return response()->json(['success' => __('submitted_successful'), 'is_reload' => true]);

            } elseif ($submitted_assignment->status == 0) {
                $request['user_id']       = auth()->user()->id;
                $request['assignment_id'] = $request->assignment_id;
                $submitedAssignmentRepository->update($request->all(), $submitted_assignment->id);
                Toastr::success(__('submitted_successful'));

                return response()->json(['success' => __('submitted_successful'), 'is_reload' => true]);
            } else {
                Toastr::success(__('not_allowed'));

                return response()->json(['error' => __('not_allowed')]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function submittedAssignmentDelete(Request $request, SubmitedAssignmentRepository $submitedAssignmentRepository)
    {

        $submitted_id = $request->id;
        try {
            $submitedAssignmentRepository->delete($submitted_id);
            $response = [
                'status'  => 'success',
                'title'   => __('success'),
                'message' => __('delete_successful'),
            ];

            return response()->json($response);
        } catch (Exception $e) {
            $response = [
                'status'  => 400,
                'title'   => 'error',
                'message' => __($e->getMessage()),
            ];

            return response()->json($response);
        }
    }

    public function certificate(Request $request, CertificateRepository $certificateRepository, CourseRepository $courseRepository, QuizRepository $quizRepository)
    {
        $user_id             = auth()->user()->id;
        $courses_certificate = $courseRepository->activeCourses([
            'my_course'   => 1,
            'user_id'     => $user_id,
            'paginate'    => '',
            'course_view' => setting('course_view_percent'),
        ], ['enrolls'])->count();
        $total_field_quiz    = 0;
        $total_pass_quiz     = 0;
        $answers             = QuizAnswer::where('user_id', $user_id)->groupBy('quiz_id')->get();
        foreach ($answers as $answer) {
            $quiz           = $quizRepository->find($answer->quiz_id);
            $correct_answer = QuizAnswer::where([
                ['user_id', $user_id],
                ['quiz_id', $quiz->id],
            ])->whereRaw(DB::raw('answers = correct_answer'))->where('answers', '!=', null)->count();
            $question_mark  = ($quiz->total_marks / count($quiz->questions));
            $found_mark     = ($correct_answer * $question_mark);
            if ($quiz->pass_marks <= $found_mark) {
                $total_pass_quiz = +1;
            }
            if ($quiz->pass_marks > $found_mark) {
                $total_field_quiz = +1;
            }
        }

        $courses             = $this->course->activeCourses([
            'my_course'   => 1,
            'user_id'     => auth()->id(),
            'paginate'    => setting('paginate'),
            'course_view' => setting('course_view_percent'),
        ], ['enrolls']);

        if ($courses->previousPageUrl()) {
            if ($courses->onLastPage()) {
                $total_results = $courses->total();
            } else {
                $total_results = $request->page * $courses->perPage();
            }
        }

        if ($courses->onFirstPage()) {
            $total_results = $courses->count();
        }

        if (request()->ajax()) {
            $course_view = '';
            foreach ($courses as $course) {
                $vars = [
                    'course' => $course,
                ];
                $course_view .= view('frontend.profile.components.certificate_course', $vars)->render();
            }

            return response()->json([
                'success'       => true,
                'html'          => $course_view,
                'next_page'     => $courses->nextPageUrl(),
                'total_results' => $total_results ?? 0,
                'total_courses' => $courses->total(),
            ]);
        }

        $updatedCourses      = [];

        foreach ($courses as $course) {
            if ($course->progress > setting('course_view_percent')) {
                $updatedCourses[] = $course;
            }
        }

        $data                = [
            'nextPageUrl'         => $courses->nextPageUrl(),
            'currentPage'         => $courses->currentPage(),
            'courses'             => $updatedCourses,
            'courses_certificate' => $courses_certificate,
            'total_courses'       => $courses->total(),
            'total_results'       => $total_results ?? 0,
            'total_pass_quiz'     => $total_pass_quiz,
            'total_field_quiz'    => $total_field_quiz,
        ];

        return view('frontend.certificate', $data);
    }

    public function certificateShow($id)
    {
        try {

            $course = $this->course->find($id);
            $data   = [
                'course' => $course,
            ];

            return view('frontend.course_certificate', $data);
        } catch (\Exception $e) {
            Toastr::error($e->getMessage());

            return back();
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
        } catch (\Matrix\Exception $e) {
            Toastr::warning($e->getMessage());

            return redirect()->back();
        }
    }
	
	public function resourceDownload($id): \Symfony\Component\HttpFoundation\BinaryFileResponse|RedirectResponse
	{
		try {
			$user = auth()->user();
			if ($user->role_id === 1) {
				// If the user is an admin, allow them to access the resource
				$resource = Resource::find($id);
			} else {
				// Otherwise, check the usual condition for enrolled users
				$resource  = Resource::whereHas('course', function ($query) {
					$query->whereHas('enrolls.checkout', function ($query) {
						$query->where('user_id', auth()->id());
					});
				})->find($id);
			}

			if (! $resource) {
				abort(404);
			}

			$file_path = public_path($resource->source);
			$extension = explode('.', $file_path)[1];

			return response()->download($file_path, $resource->slug . ".$extension");
		} catch (Exception $e) {
			Toastr::error($e->getMessage());

			return back();
		}
	}

    public function oneSignalSubscribe(Request $request): JsonResponse
    {
        try {
            $user                          = auth()->user();

            if ($request->player_id) {
                $user->onesignal_player_id = $request->player_id;
            }

            $user->is_onesignal_subscribed = $request->subscribed;
            $user->save();

            return response()->json([
                'success' => 'OneSignal subscription status updated successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
}