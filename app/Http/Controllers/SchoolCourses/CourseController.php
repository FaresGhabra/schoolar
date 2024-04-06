<?php

namespace App\Http\Controllers\SchoolCourses;

use App\Http\Controllers\Controller;
use App\Models\SchoolCourses\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{

    public function index(Request $request)
    {

        $perPage = $request->query('per_page', 10);

        $courses = Course::searchable($request)->orderable($request)->paginate($perPage);

        sendJson($courses);
    }


    public function show(string $id)
    {
        $course = Course::with(['videos', 'files'])->find($id);

        if (!$course)
            sendMessageJson("Course not found", 404);

        return response()->json($course, 200);
    }
}