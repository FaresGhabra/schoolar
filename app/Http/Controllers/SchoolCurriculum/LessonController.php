<?php

namespace App\Http\Controllers\SchoolCurriculum;

use App\Http\Controllers\Controller;
use App\Models\SchoolCurriculum\Lesson;
use App\Models\SchoolCurriculum\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $lessons = Lesson::with('subject')
            ->searchable($request)
            ->searchableWith($request, 'subject')
            ->paginate($perPage);

        sendJson($lessons);
    }

    public function subject_lessons(Request $request, $subject_id)
    {
        $subject = Subject::find($subject_id);
        if (!$subject)
            sendMessageJson("Subect not found", 404);

        $perPage = $request->query('per_page', 10);
        $lessons = Lesson::where("subject_id", $subject_id)
            ->searchable($request)
            ->paginate($perPage);

        sendJson($lessons);
    }

    public function createMany(Request $request, $subject_id) {
        $subject = Subject::find($subject_id);
        if (!$subject)
            sendMessageJson("Subject not found", 404);

        $validData = $request->validate([
            'lessons' => 'required|array',
            'lessons.*.name' => [
                'required',
                'string',
            ],
            'lessons.*.number' => [
                'required',
                'numeric',
            ],
            'lessons.*.unit' => 'required|numeric'
        ]);

        $lessons = $subject->lessons()->createMany($validData['lessons']);

        sendJson($lessons);
    }

    public function create(Request $request, $subject_id)
    {
        $subject = Subject::find($subject_id);
        if (!$subject)
            sendMessageJson("Subject not found", 404);

        $validData = $request->validate([
            'name' => [
                'required',
                'string', Rule::unique('lessons')->where(function ($query) use ($request, $subject_id) {
                    return $query
                        ->where('name', $request->name)
                        ->where('subject_id', $subject_id);
                })
            ],
            'number' => [
                'required',
                'numeric', Rule::unique('lessons')->where(function ($query) use ($request, $subject_id) {
                    return $query
                        ->where('number', $request->number)
                        ->where('subject_id', $subject_id);
                })
            ],
            'unit' => 'required|numeric'
        ]);

        $lesson = $subject->lessons()->create($validData);

        sendJson($lesson);
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::find($id);
        if (!$lesson)
            sendMessageJson("Lesson not found", 404);
        $subject_id = $lesson->subject_id;
        $validData = $request->validate([
            'name' => [
                'sometimes',
                'string', Rule::unique('lessons')->where(function ($query) use ($request, $subject_id) {
                    return $query->where('name', $request->name)
                        ->where('subject_id', $subject_id);
                })
            ],
            'number' => [
                'sometimes',
                'numeric', Rule::unique('lessons')->where(function ($query) use ($request, $subject_id) {
                    return $query->where('name', $request->name)
                        ->where('number', $request->number)
                        ->where('subject_id', $subject_id);
                })
            ],
            'unit' => 'sometimes|numeric'
        ]);
        $lesson->update($validData);
        $lesson->save();
        sendJson($lesson);
    }

    public function delete(Request $request, $id)
    {
        $lesson = Lesson::find($id);
        if (!$lesson)
            sendMessageJson("Lesson not found", 404);
        $lesson->delete();
        sendMessageJson("Lesson has been deleted successflly");
    }
}