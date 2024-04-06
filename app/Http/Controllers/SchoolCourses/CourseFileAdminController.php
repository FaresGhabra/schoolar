<?php

namespace App\Http\Controllers\SchoolCourses;

use App\Http\Controllers\Controller;
use App\Models\SchoolCourses\Course;
use App\Models\SchoolCourses\CourseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Helpers\FileHelper;

class CourseFileAdminController extends Controller
{

    public function index(Request $request, $course_id)
    {
        $course = Course::find($course_id);

        if (!$course) {
            sendMessageJson('Course not found', 404);
        }

        $files = $course->files;

        sendJson($files);
    }

    public function show($course_id, $id)
    {
        $file = CourseFile::find($id);
        $course = Course::find($course_id);

        if (!$file || !$course) sendMessageJson('Course file not found', 404);

        sendJson($file);
    }
    public function store(Request $request, $course_id)
    {
        $course = Course::find($course_id);

        if (!$course)
            sendMessageJson("Course not found", 404);

        $validData = $request->validate([
            'note' => 'max:255',
        ]);

        $validData['course_id'] = $course_id;
        $file = CourseFile::create($validData);

        sendJson($file);
    }

    public function storeFile(Request $request, $course_id, $id)
    {
        $course = Course::find($course_id);
        $courseFile = CourseFile::find($id);

        if (!$course || !$courseFile)
            sendMessageJson('Course or file not found', 404);

        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
        $fileReceived = $receiver->receive();

        if ($fileReceived->isFinished()) {
            $file = $fileReceived->getFile();
            $extension = $file->getClientOriginalExtension();
            $filename = 'file_' . time() . '.' . $extension;

            $path = 'courses/' . $course->folder . '/files/';

            if (file_exists(Storage::path($courseFile->file)))
                unlink(Storage::path($courseFile->file));
            $file->storeAs($path, $filename);
            unlink($file->getPathname());
            $courseFile->file = $path . $filename;
            $courseFile->save();

            sendJson($courseFile);
        }

        $handler = $fileReceived->handler();
        sendJson([
            'done' => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    public function update(Request $request, $course_id, $id)
    {
        $validData = $request->validate([
            'note' => 'required|sometimes|max:255',
        ]);

        $course = Course::find($course_id);
        $file = CourseFile::find($id);
        if (!$file || !$course)
            sendMessageJson('Course or file not found', 404);

        $file->course_id = $course_id;
        $file->update($validData);

        $file->save();

        sendJson($file);
    }

    public function destroy($course_id, $id)
    {
        $course = Course::find($course_id);
        $file = CourseFile::find($id);

        if (!$file || !$course)
            sendMessageJson('Course or video not found', 404);

        $file->delete();

        $message = "the file has been deleted successfully";

        sendMessageJson($message);
    }
}