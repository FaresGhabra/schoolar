<?php

use App\Http\Controllers\AuthController;
use \App\Http\Controllers\PaymentController;
use App\Http\Controllers\SchoolAccounts\TeacherController;
use App\Http\Controllers\SchoolClassStructure\ClassSectionController;
use App\Http\Controllers\SchoolClassStructure\SchoolClassController;
use App\Http\Controllers\SchoolCourses\CourseAdminController;
use App\Http\Controllers\SchoolCourses\CourseFileAdminController;
use App\Http\Controllers\SchoolCourses\CourseVideoAdminController;
use App\Http\Controllers\SchoolCourses\CourseController;
use App\Http\Controllers\SchoolAccounts\OnlineStudentController;
use App\Http\Controllers\SchoolAccounts\SchoolStaffController;
use App\Http\Controllers\SchoolAccounts\StudentController;
use App\Http\Controllers\SchoolAccounts\StuParentController;
use App\Http\Controllers\SchoolCoursework\ExamController;
use App\Http\Controllers\SchoolCoursework\ExamMarkController;
use App\Http\Controllers\SchoolCoursework\HomeworkController;
use App\Http\Controllers\SchoolCoursework\HomeworkMarkController;
use App\Http\Controllers\SchoolCurriculum\LessonController;
use App\Http\Controllers\SchoolCurriculum\SubjectController;
use App\Http\Controllers\SchoolInfoController;
use App\Http\Controllers\SchoolResources\ResourceAdminController;
use App\Http\Controllers\SchoolResources\ResourceController;
use App\Http\Controllers\SchoolServices\ServiceAdminController;
use App\Http\Controllers\SchoolServices\ServiceController;
use App\Http\Controllers\SchoolSchedules\ProgramAdminController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;




/**
 * Auth Routes
 */

Route::post('/login', [AuthController::class, 'login']);

Route::post('/user', [AuthController::class, 'show'])
    ->middleware(['auth:api']);

/**
 * Public Courses routes
 */

Route::get('courses/', [CourseController::class, 'index'])
    ->middleware(['auth:api', 'role:online_student|parent|student']);

Route::get('courses/{id}', [CourseController::class, 'show'])
    ->middleware(['auth:api', 'role:online_student|parent|student']);

Route::get('courses/{course_folder}/videos/{video_file_name}/thumb.jpg', [CourseVideoAdminController::class, 'viewThumb']);

Route::get('courses/{course_folder}/{thumb}', [CourseAdminController::class, 'viewThumb']);

Route::get('courses/{course_folder}/videos/{video_file_name}', [CourseVideoAdminController::class, 'viewVideo']);


/**
 * Online Student routes
 */

Route::get('online_student/', [OnlineStudentController::class, 'index'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('online_student/{id}/profile', [OnlineStudentController::class, 'show'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('online_student/profile', [OnlineStudentController::class, 'show'])
    ->middleware(['auth:api']);

Route::post('online_student/register', [OnlineStudentController::class, 'register'])
    ->middleware(['guest']);

Route::post('online_student/update_profile', [OnlineStudentController::class, 'update'])
    ->middleware(['auth:api', 'role:online_student']);

/**
 * Student Routes
 */

Route::get('students/{id}/profile', [StudentController::class, 'show'])
    ->middleware(['auth:api']);

/**
 * Parent Routes
 */

Route::get('parents/{id}/profile', [StuParentController::class, 'show'])
    ->middleware(['auth:api']);

Route::get('parents/children', [StuParentController::class, 'showChildren'])
    ->middleware(['auth:api', 'role:parent']);


/**
 * Teacher Routes
 */
Route::get('teachers/{id}', [TeacherController::class, 'show'])
    ->middleware(['auth:api', 'role:owner|admin|teacher']);

Route::get('teachers/{id}/sections', [TeacherController::class, 'getTeacherSections'])
    ->middleware(['auth:api', 'role:owner|admin|teacher']);

Route::get('teachers/{id}/subjects', [TeacherController::class, 'getTeacherSubjects'])
    ->middleware(['auth:api', 'role:owner|admin|teacher']);

/**
 * Services Routes
 */

Route::get('services', [ServiceController::class, 'index'])
    ->middleware(['auth:api', 'role:parent']);

Route::get('services/my-services', [ServiceController::class, 'viewMySerivces'])
    ->middleware(['auth:api', 'role:parent']);

Route::get('services/{id}', [ServiceController::class, 'show'])
    ->middleware(['auth:api', 'role:parent']);

/**
 * Resources Routes
 */

Route::get('resources', [ResourceController::class, 'index'])
    ->middleware(['auth:api', 'role:teacher|student']);

Route::get('resources/subjects/{subject_id}', [ResourceController::class, 'showClassSubject'])
    ->middleware(['auth:api', 'role:teacher|student']);

Route::get('resources/{id}', [ResourceController::class, 'show'])
    ->middleware(['auth:api', 'role:teacher|student']);

Route::post('resources', [ResourceAdminController::class, 'store'])
    ->middleware(['auth:api', 'role:teacher|student']);

Route::post('resources/{id}/edit', [ResourceAdminController::class, 'update'])
    ->middleware(['auth:api', 'role:teacher']);

Route::post('resources/{id}/delete', [ResourceAdminController::class, 'delete'])
    ->middleware(['auth:api', 'role:teacher']);


/**
 * Schduels
 */

Route::get('schedules/sections/{section_id}', [ProgramAdminController::class, 'showSectionProgram'])
    ->middleware(['auth:api', 'role:student']);

Route::get('schedules/teachers/{teacher_id}', [ProgramAdminController::class, 'showTeacherProgram'])
    ->middleware(['auth:api', 'role:teacher']);


/**
 * Homework Routes
 */

Route::get('homeworks/teacher', [HomeworkController::class, 'showTeacherAllHomeworks'])
    ->middleware(['auth:api', 'role:teacher']);

Route::get('homeworks/teacher/new', [HomeworkController::class, 'showTeacherNewHomeworks'])
    ->middleware(['auth:api', 'role:teacher']);

Route::get('homeworks/teacher/undone', [HomeworkController::class, 'showTeacherUndoneHomeworks'])
    ->middleware(['auth:api', 'role:teacher']);

Route::get('homeworks/teacher/old', [HomeworkController::class, 'showTeacherOldHomeworks'])
    ->middleware(['auth:api', 'role:teacher']);

Route::get('homeworks/section/{section_id}/new', [HomeworkController::class, 'showSectionNewHomworks'])
    ->middleware(['auth:api', 'role:student|teacher']);

Route::get('homeworks/section/{section_id}/old', [HomeworkController::class, 'showSectionOldHomworks'])
    ->middleware(['auth:api', 'role:student|teacher']);

Route::get('homeworks/section/{section_id}/', [HomeworkController::class, 'showSectionAllHomworks'])
    ->middleware(['auth:api', 'role:student|teacher']);

Route::get('homeworks/{id}', [HomeworkController::class, 'show'])
    ->middleware(['auth:api', 'role:student|teacher']);

Route::post('homeworks/{id}/delete', [HomeworkController::class, 'delete'])
    ->middleware(['auth:api', 'role:teacher']);

Route::post('homeworks/{id}/edit', [HomeworkController::class, 'update'])
    ->middleware(['auth:api', 'role:teacher']);

Route::post('homeworks/', [HomeworkController::class, 'store'])
    ->middleware(['auth:api', 'role:teacher']);

/**
 * Homework Marks Routes {Fares}
 */

Route::post("homeworks/marks/sections/{section_id}", [HomeworkMarkController::class, 'storeMarksForSection'])
    ->middleware(['auth:api', 'role:teacher']);

Route::post("homeworks/marks/students/{student_id}", [HomeworkMarkController::class, 'storeMarkForStudent'])
    ->middleware(['auth:api', 'role:teacher']);

Route::get("homeworks/{id}/marks/", [HomeworkMarkController::class, 'getMarks'])
    ->middleware(['auth:api', 'role:teacher']);

Route::get("homeworks/marks/student/{id}", [HomeworkMarkController::class, 'getStudentMarks'])
    ->middleware(['auth:api', 'role:teacher|student']);

/**
 * Exam Marks Routes
 */
Route::get('exams/{id}/marks/students/{s_id}', [ExamMarkController::class, 'getStudentMark'])
    ->middleware(['auth:api', 'role:student|parent']);

Route::get('exams/marks/students/{s_id}', [ExamMarkController::class, 'getStudentMarks'])
    ->middleware(['auth:api', 'role:student|parent']);


/**
 * Invoices Routes
 */

Route::get('invoice/services/admin', [InvoiceController::class, 'InvServiceA'])
    ->middleware(['auth:api', 'role:admin,owner']);

Route::get('invoice/courses/admin', [InvoiceController::class, 'InvCourseA'])
    ->middleware(['auth:api', 'role:admin|owner']);

Route::get('invoice/courses/parent', [InvoiceController::class, 'InvCourseP'])
    ->middleware(['auth:api', 'role:parent']);

Route::get('invoice/services/parent', [InvoiceController::class, 'InvServiceP'])
    ->middleware(['auth:api', 'role:parent']);

Route::post('invoice/pay', [InvoiceController::class, 'payService'])
    ->middleware(['auth:api', 'role:parent,admin']);

/**
 * Messages Routes
 */

Route::post('/messages/send', [MessageController::class, 'postMessage'])
    ->middleware(['auth:api', 'role:prompt,admin,owner']);

Route::get('/messages', [MessageController::class, 'getMessages'])
    ->middleware(['auth:api', 'role:parent']);


/** 
 * School Info
 */

Route::get('school_info', [SchoolInfoController::class, 'index']);

Route::post('school_info/edit', [SchoolInfoController::class, 'edit']);

Route::post('school_info/edit_images', [SchoolInfoController::class, 'udpateImages']);

/**
 * School Structure
 */

Route::get('classes', [SchoolClassController::class, 'index'])
    ->middleware(['auth:api']);

Route::get('subjects/class/{class_id}', [SubjectController::class, 'class_subjects'])
    ->middleware(['auth:api']);



/**
 * School Schedules Routes
 */

Route::post('dashboard/schedules/sections/{section_id}', [ProgramAdminController::class, 'updateSection'])
    ->middleware(['auth:api', 'role:admin|owner']);

Route::get('dashboard/schedules/sections/{section_id}', [ProgramAdminController::class, 'showSectionProgram'])
    ->middleware(['auth:api', 'role:admin|owner']);

Route::get('dashboard/schedules/teachers/{teacher_id}', [ProgramAdminController::class, 'showTeacherProgram'])
    ->middleware(['auth:api', 'role:admin|owner']);

Route::post('dashboard/schedules/sections/{section_id}/day/{day}/session/{session}', [ProgramAdminController::class, 'update'])
    ->middleware(['auth:api', 'role:admin|owner']);

/*******************************************
 *                  DASHBOARD
 * *****************************************
 */


/**
 * Student Routes
 */

Route::get('dashboard/students/', [StudentController::class, 'index'])
    ->middleware(['auth:api', 'role:admin|owner']);

Route::get('dashboard/students/{id}', [StudentController::class, 'show'])
    ->middleware(['auth:api', 'role:admin|owner']);

Route::post('dashboard/students/', [StudentController::class, 'create'])
    ->middleware(['auth:api', 'role:admin|owner']);

Route::post('dashboard/students/{id}/edit', [StudentController::class, 'update'])
    ->middleware(['auth:api', 'role:admin|owner']);

Route::post('dashboard/students/record', [StudentController::class, 'studentRecord'])
    ->middleware(['auth:api', 'role:admin|owner']);

Route::post('dashboard/students/{id}/deactive', [StudentController::class, 'deactive'])
    ->middleware(['auth:api', 'role:admin|owner']);


/**
 * Parent Routes
 */

Route::get('dashboard/parents/', [StuParentController::class, 'index'])
    ->middleware(['auth:api', 'role:admin|owner']);

Route::post('dashboard/parents/', [StuParentController::class, 'create'])
    ->middleware(['auth:api', 'role:admin|owner']);

Route::get('dashboard/parents/{id}', [StuParentController::class, 'show'])
    ->middleware(['auth:api', 'role:admin|owner']);

Route::post('dashboard/parents/{id}/edit', [StuParentController::class, 'update'])
    ->middleware(['auth:api', 'role:admin|owner']);

/***
 * Courses Management Routes
 */
// Courses routes

Route::get('dashboard/courses', [CourseAdminController::class, 'index'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('dashboard/courses/{id}', [CourseAdminController::class, 'show'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/courses/', [CourseAdminController::class, 'store'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/courses/{id}', [CourseAdminController::class, 'update'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/courses/{id}/delete', [CourseAdminController::class, 'destroy'])
    ->middleware(['auth:api', 'role:owner']);
// Course File routes

Route::get('dashboard/courses/{id}/files/', [CourseFileAdminController::class, 'index'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('dashboard/courses/{course_id}/files/{id}', [CourseFileAdminController::class, 'show'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/courses/{course_id}/files/', [CourseFileAdminController::class, 'store'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/courses/{course_id}/files/{id}/edit', [CourseFileAdminController::class, 'update'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/courses/{course_id}/files/{id}/file', [CourseFileAdminController::class, 'storeFile'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/courses/{course_id}/files/{id}/delete', [CourseFileAdminController::class, 'destroy'])
    ->middleware(['auth:api', 'role:owner']);
// Course Video routes

Route::get('dashboard/courses/{id}/videos/', [CourseVideoAdminController::class, 'index'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('dashboard/courses/{course_id}/videos/{id}', [CourseVideoAdminController::class, 'show'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/courses/{course_id}/videos/', [CourseVideoAdminController::class, 'store'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/courses/{course_id}/videos/{id}/edit', [CourseVideoAdminController::class, 'update'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/courses/{course_id}/videos/{id}/video', [CourseVideoAdminController::class, 'storeVideo'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/courses/{course_id}/videos/{id}/delete', [CourseVideoAdminController::class, 'destroy'])
    ->middleware(['auth:api', 'role:owner']);


/**
 * Online Students
 */

Route::get('dashboard/online_students/', [OnlineStudentController::class, 'index'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('dashboard/online_students/{id}', [OnlineStudentController::class, 'show'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/online_students/{id}/edit', [OnlineStudentController::class, 'update'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/online_students/{id}/delete', [OnlineStudentController::class, 'destroy'])
    ->middleware(['auth:api', 'role:owner']);

/**
 * School Staff Routes
 */

Route::get('dashboard/staff/', [SchoolStaffController::class, 'index'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('dashboard/staff/{id}', [SchoolStaffController::class, 'show'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/staff/', [SchoolStaffController::class, 'create'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/staff/{id}/edit', [SchoolStaffController::class, 'update'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/staff/teacher/{id}/assign', [SchoolStaffController::class, 'assignTeacher'])
    ->middleware(['auth:api', 'role:owner']);

/**
 * School Class Structure Routes
 */
// Class

Route::get('dashboard/classes/', [SchoolClassController::class, 'index'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('dashboard/classes/names', [SchoolClassController::class, 'dashboard/classesNames'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('dashboard/classes/{id}/', [SchoolClassController::class, 'show'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/classes/', [SchoolClassController::class, 'create'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/classes/{id}/edit', [SchoolClassController::class, 'update'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/classes/{id}/delete', [SchoolClassController::class, 'delete'])
    ->middleware(['auth:api', 'role:owner']);

// Section

Route::get('dashboard/classes/~/sections', [ClassSectionController::class, 'index'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/classes/{id}/sections/', [ClassSectionController::class, 'create'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/classes/{class_id}/sections/{id}/edit', [ClassSectionController::class, 'update'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/classes/{class_id}/sections/{id}/delete', [ClassSectionController::class, 'delete'])
    ->middleware(['auth:api', 'role:owner']);


/**
 * School Curriculum Routes
 */

// Subject

Route::get('dashboard/subjects', [SubjectController::class, 'index'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('dashboard/subjects/class/{class_id}', [SubjectController::class, 'class_dashboard/subjects'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/subjects/', [SubjectController::class, 'create'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/subjects/edit', [SubjectController::class, 'update'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/subjects/{id}/edit', [SubjectController::class, 'update'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/subjects/delete/', [SubjectController::class, 'delete'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/subjects/{id}/delete', [SubjectController::class, 'delete'])
    ->middleware(['auth:api', 'role:owner']);

// Lesson

Route::get('dashboard/lessons', [LessonController::class, 'index'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('dashboard/lessons/subject/{subject_id}', [LessonController::class, 'subject_lessons'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/lessons/subject/{subject_id}', [LessonController::class, 'create'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/lessons/subject/{subject_id}/add_lessons', [LessonController::class, 'createMany'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/lessons/{id}/edit', [LessonController::class, 'update'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/lessons/{id}/delete', [LessonController::class, 'delete'])
    ->middleware(['auth:api', 'role:owner']);


/**
 * School Services Routes
 */

Route::get('dashboard/services', [ServiceAdminController::class, 'index'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('dashboard/services/{id}', [ServiceAdminController::class, 'show'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('dashboard/services/user/{id}', [ServiceAdminController::class, 'viewUserServices'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/services/', [ServiceAdminController::class, 'store'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/services/{id}/photos', [ServiceAdminController::class, 'updateImages'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/services/{id}/edit', [ServiceAdminController::class, 'update'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/services/{id}/delete', [ServiceAdminController::class, 'delete'])
    ->middleware(['auth:api', 'role:owner']);

/**
 * School Resources Routes
 */

Route::get('dashboard/resources', [ResourceAdminController::class, 'index'])
    ->middleware(['auth:api', 'role:owner']);

Route::get('dashboard/resources/{id}', [ResourceAdminController::class, 'show'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/resources', [ResourceAdminController::class, 'store'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/resources/{id}/edit', [ResourceAdminController::class, 'update'])
    ->middleware(['auth:api', 'role:owner']);

Route::post('dashboard/resources/{id}/delete', [ResourceAdminController::class, 'delete'])
    ->middleware(['auth:api', 'role:owner']);

/**
 * Exam Routes
 */

Route::get('dashboard/exams/', [ExamController::class, 'index'])
    ->middleware(['auth:api', 'role:owner|admin|prompt|student|parent']);

Route::get('dashboard/exams/classes', [ExamController::class, 'classesWithExams'])
    ->middleware(['auth:api', 'role:owner|admin|prompt|student|parent']);

Route::post('dashboard/exams/', [ExamController::class, 'store'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::post('dashboard/exams/class/{class_id}', [ExamController::class, 'storeExamProgramForClass'])
    ->middleware(['auth:api', 'role:owner|admin']);

Route::post('dashboard/exams/{id}/edit', [ExamController::class, 'update'])
    ->middleware(['auth:api', 'role:owner|admin']);

Route::post('dashboard/exams/{id}/delete', [ExamController::class, 'delete'])
    ->middleware(['auth:api', 'role:owner|admin']);

Route::get('dashboard/exams/class/{class_id}', [ExamController::class, 'showAllExamsOfClass'])
    ->middleware(['auth:api', 'role:owner|admin|prompt|student|parent']);

Route::get('dashboard/exams/class/{class_id}/new', [ExamController::class, 'showNewExamsOfClass'])
    ->middleware(['auth:api', 'role:owner|admin|prompt|student|parent']);

Route::get('dashboard/exams/class/{class_id}/old', [ExamController::class, 'showOldExamsOfClass'])
    ->middleware(['auth:api', 'role:owner|admin|prompt|student|parent']);


/**
 * Exam Marks Routes
 */

Route::post("dashboard/exams/marks/sections/{section_id}", [ExamMarkController::class, 'storeMarksForSection'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::post("dashboard/exams/marks/students/{student_id}", [ExamMarkController::class, 'storeMarkForStudent'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::get("dashboard/exams/{id}/marks/classes/{class_id}", [ExamMarkController::class, 'getClassMarks'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::get("dashboard/exams/{id}/marks/sections/{section_id}", [ExamMarkController::class, 'getSectionMarks'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::get("dashboard/exams/finished/", [ExamMarkController::class, 'getFinishedExams'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::get("dashboard/exams/unfinished/", [ExamMarkController::class, 'getUnfinishedExams'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::get("dashboard/exams/undone/", [ExamMarkController::class, 'getUndoneExams'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::get("dashboard/exams/{id}", [ExamMarkController::class, 'getExamMarks'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::get("dashboard/exams/{id}/undone", [ExamMarkController::class, 'getUndoneMarks'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);


Route::post('/absence/student', [AbsenceController::class, 'stuAbsence'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::post('/absence/teacher', [AbsenceController::class, 'teacherAbsence'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::post('/absence/edit', [AbsenceController::class, 'EditAbsence'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::get('/absence/get', [AbsenceController::class, 'getAbsence'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

Route::get('/absence/get_section', [AbsenceController::class, 'getAbsenceSection'])
    ->middleware(['auth:api', 'role:owner|admin|prompt']);

/**
 * Notes From Teacher Rotues
 */
Route::post('/note/add', [TeacherNoteController::class, 'addNote'])
    ->middleware(['auth:api', 'role:teacher']);

/**
 * Telegram Routes
 */

Route::get('/send', [TelegramController::class, 'send_absence_message'])
    ->middleware(['auth:api', 'role:parent']);

Route::get('/chat_id', [TelegramController::class, 'set_chat_id'])
    ->middleware(['auth:api', 'role:admin']);


/**
 * Bus Supervisor Routes
 */
Route::get('/bus-supervisors', [TelegramController::class, 'index'])
    ->middleware(['auth:api', 'role:owner|admin']);

Route::post('/bus-supervisors', [TelegramController::class, 'store'])
    ->middleware(['auth:api', 'role:owner|admin']);

Route::get('/bus-supervisors/{id}', [TelegramController::class, 'show'])
    ->middleware(['auth:api', 'role:owner|admin']);

Route::put('/bus-supervisors/{id}', [TelegramController::class, 'update'])
    ->middleware(['auth:api', 'role:owner|admin']);

Route::delete('/bus-supervisors/{id}', [TelegramController::class, 'destroy'])
    ->middleware(['auth:api', 'role:owner|admin']);



/**
 * Payment Routes
 */
Route::group(['prefix' => 'payment'], function () {
    Route::post('/payment', [PaymentController::class, 'pay'])->name('payment');

    Route::match(['GET', 'POST'], 'payment-order-callback/', [PaymentController::class, 'call_back'])->name('payment.callback');

    Route::match(['GET', 'POST'], '/callback/redirect', [PaymentController::class, 'redirectAfterPayment'])->name('payment.redirect');
});

// Error 404 for undefined routes
Route::fallback(function () {
    sendMessageJson('Error 404', 404);
});