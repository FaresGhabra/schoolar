<?php

namespace App\Http\Controllers\SchoolCourses;

use App\Http\Controllers\Controller;
use App\Models\SchoolCourses\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class CourseAdminController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $courses = Course::searchable($request)->orderable($request)->
            paginate($perPage);

        sendJson($courses);
    }


    public function show(string $id)
    {
        $course = Course::find($id);
        if (!$course) {
            sendMessageJson('Course not found', 404);
        }
        $course->load(['files', 'videos']);

        sendJson($course);
    }

    public function store(Request $request)
    {
        $validData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'thumb_file' => 'required|file|image',
            'price' => 'required|numeric',
            'tags' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'author' => 'required|string',
        ]);
        $course = Course::create($validData);

        $string = $course->title;
        $string = str_replace(' ', '_', $string);
        $string = str_replace('-', '_', $string);
        $string = strtolower($string);
        $pattern = '/[^a-zA-Z_0-9]/';
        $string = preg_replace($pattern, '', $string);
        $string = time() . '_' . $string;
        $course->folder = $string;

        if (isset($validData['thumb_file'])) {
            $extension = $validData['thumb_file']->getClientOriginalExtension();
            $filename = 'thumb_' . time() . '.' . $extension;
            $path = $validData['thumb_file']->storeAs('courses/' . $course->folder, $filename);
            $course->thumb = $path;
        }

        $course->save();

        sendJson($course);
    }

    public function viewThumb(Request $request, $course_folder, $thumb)
    {
        $course = Course::where('folder', $course_folder)->get()->first();
        if (!$course)
            sendMessageJson("Course not found", 404);
        
        $path = "courses/{$course->folder}/{$thumb}";
        $courseVideo = Course::where('thumb', 'like', $path . '%')->get()->first();
        if (!$courseVideo)
            sendMessageJson("Thumb not found", 404);

        $file_path = Storage::path($path);
        return response()->file($file_path);
    }


    public function update(Request $request, string $id)
    {
        $course = Course::find($id);

        if (!$course)
            sendMessageJson("Course not found", 404);

        $validData = $request->validate([
            'title' => 'sometimes|required|max:255',
            'description' => 'sometimes|required',
            'thumb_file' => 'sometimes|nullable|image',
            'price' => 'sometimes|required|numeric|min:0',
            'tags' => 'sometimes|required|string',
            'subtitle' => 'sometimes|required|max:255',
            'author' => 'sometimes|required|max:255',
        ]);

        $course->update($validData);

        if (!$course->folder) {
            $string = $course->title;
            $string = str_replace(' ', '_', $string);
            $string = str_replace('-', '_', $string);
            $string = strtolower($string);
            $pattern = '/[^a-zA-Z_0-9]/';
            $string = preg_replace($pattern, '', $string);
            $string = time() . '_' . $string;
            $course->folder = $string;
        }

        if (file_exists(Storage::path($course->thumb))) {
            unlink(Storage::path($course->thumb));
        }

        if (isset($validData['thumb_file'])) {
            $extension = $validData['thumb_file']->getClientOriginalExtension();
            $filename = 'thumb_' . time() . '.' . $extension;
            $path = $validData['thumb']->storeAs('courses/' . $course->folder, $filename);
            $course->thumb = $path;
        }

        $course->save();

        sendJson($course);
    }

    public function destroy(string $id)
    {
        $course = Course::find($id);

        if ($course === null) {
            return response()->json([
                'message' => 'error: Course not found',
            ], 404);
        }

        $course->delete();

        $message = "the course has been deleted successfully";
        sendMessageJson($message);
    }
}