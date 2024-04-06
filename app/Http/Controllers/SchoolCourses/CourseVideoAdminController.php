<?php

namespace App\Http\Controllers\SchoolCourses;

use App\Http\Controllers\Controller;
use App\Helpers\FileHelper;
use App\Jobs\ConvertVideoJob;
use App\Models\SchoolCourses\CourseVideo;
use App\Models\SchoolCourses\Course;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;


class CourseVideoAdminController extends Controller
{

    public function index(Request $request, $course_id)
    {
        $course = Course::find($course_id);

        if (!$course) {
            sendMessageJson('Course not found', 404);
        }

        $files = $course->videos;

        sendJson($files);
    }

    public function show($course_id, $id)
    {
        $video = CourseVideo::find($id);
        $course = Course::find($course_id);

        if (!$video || !$course)
            sendMessageJson('Course video not found', 404);

        sendJson($video);
    }

    public function store(Request $request, $course_id)
    {
        $validData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
        ]);

        $course = Course::find($course_id);
        if (!$course)
            sendMessageJson('Course not found', 404);

        $validData['course_id'] = $course_id;
        $video = CourseVideo::create($validData);
        sendJson($video);
    }

    public function storeVideo(Request $request, $course_id, $video_id)
    {
        $course = Course::find($course_id);
        $courseVideo = CourseVideo::find($video_id);

        if (!$course || !$courseVideo)
            sendMessageJson("Course or video not found", 404);

        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
        $fileReceived = $receiver->receive();

        if ($fileReceived->isFinished()) {
            $file = $fileReceived->getFile();
            $extension = $file->getClientOriginalExtension();
            $filename_no = 'video_' . time();
            $filename = $filename_no . '.' . $extension;

            $path = 'courses/' . $course->folder . '/videos/';
            $fullpath = Storage::path($path . $filename);

            if ($courseVideo->sources)
                FileHelper::deleteFile(Storage::path($courseVideo->sources));

            $file->storeAs($path, $filename);
            unlink($file->getPathname());

            $formatCheck = FileHelper::checkFormat(
                $fullpath,
                ['video/mp4', 'video/m4a', 'video/mov', 'video/3gp']
            );

            if ($formatCheck === false) {
                unlink($fullpath);
                sendMessageJson("File format is not supported", 401);
            }

            $ffmpeg = FFMpeg::create();
            $video = $ffmpeg->open($fullpath);
            $streams = $video->getStreams();
            $duration = $streams->first()->get('duration');

            if ($duration < 10)
                sendMessageJson("Video is too short", 401);

            $video->frame(TimeCode::fromSeconds(10))->save(Storage::path($path . $filename_no . '_thumbnail.jpg'));

            $courseVideo->sources = $path . $filename;
            $courseVideo->thumb = $path . $filename_no . '/thumb.jpg';
            $courseVideo->duration = $duration;
            $courseVideo->save();

            ConvertVideoJob::dispatch($path, $filename, $video_id);

            sendJson($courseVideo);
        }

        $handler = $fileReceived->handler();
        sendJson([
            'done' => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    public function viewVideo(Request $request, $course_folder, $video_file_name)
    {
        $course = Course::where('folder', $course_folder)->get()->first();
        if (!$course)
            sendMessageJson("Course not found", 404);
        // $this->authorize($course);

        $path = "courses/{$course->folder}/videos/{$video_file_name}";
        $courseVideo = CourseVideo::where('sources', $path)->get()->first();
        if (!$courseVideo)
            sendMessageJson("Video not found", 404);

        $resolution = $request->input('p');
        $resolutions = [240, 360, 480, 720];
        if (in_array($resolution, $resolutions))
            $path = "courses/{$course->folder}/videos/{$resolution}_{$video_file_name}";

        $file_path = Storage::path($path);
        return response()->file($file_path);
    }

    public function viewThumb(Request $request, $course_folder, $video_file_name)
    {
        $course = Course::where('folder', $course_folder)->get()->first();
        if (!$course)
            sendMessageJson("Course not found", 404);
        // $this->authorize($course);

        $path = "courses/{$course->folder}/videos/{$video_file_name}";
        $courseVideo = CourseVideo::where('sources', 'like', $path . '%')->get()->first();
        if (!$courseVideo)
            sendMessageJson("Video not found", 404);

        $file_path = Storage::path($path . '/thumb.jpg');
        return response()->file($file_path);
    }

    public function update(Request $request, $course_id, $id)
    {

        $validData = $request->validate([
            'title' => 'required|sometimes|max:255',
            'description' => 'required|sometimes',
        ]);
        $course = Course::find($course_id);
        $video = CourseVideo::find($id);
        if (!$video || !$course)
            sendMessageJson('Course or video not found', 404);


        $video->update($validData);
        $video->save();
        sendJson($video);
    }


    public function destroy($course_id, $id)
    {
        $course = Course::find($course_id);
        $video = CourseVideo::find($id);
        if (!$video || !$course)
            sendMessageJson("Course or video not found", 404);

        $video->delete();

        $message = "the video [ " . $video->title . " ] deleted successfully";

        sendMessageJson($message);
    }
}