<?php

namespace App\Http\Controllers\SchoolCoursework;

use App\Http\Controllers\Controller;
use App\Models\SchoolClassStructure\ClassSection;
use App\Models\SchoolClassStructure\SchoolClass;
use App\Models\SchoolCoursework\Exam;
use App\Models\SchoolCurriculum\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $exams = Exam::searchable($request)->paginate($perPage);
        sendJson($exams);
    }

    public function classesWithExams(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $classes = SchoolClass::with('new_exams')->searchable($request)->paginate($perPage);
        sendJson($classes);
    }

    public function showAllExamsOfClass(Request $request, $class_id)
    {
        $class = SchoolClass::find($class_id);
        if (!$class)
            sendMessageJson("Class not found", 404);

        sendJson($class->exams);
    }

    public function showNewExamsOfClass(Request $request, $class_id)
    {
        $class = SchoolClass::find($class_id);
        if (!$class)
            sendMessageJson("Class not found", 404);

        sendJson($class->new_exams);
    }

    public function showOldExamsOfClass(Request $request, $class_id)
    {
        $class = SchoolClass::find($class_id);
        if (!$class)
            sendMessageJson("Class not found", 404);

        sendJson($class->old_exams);
    }

    public function store(Request $request)
    {
        $validData = $request->validate([
            'subject_id' => 'exists:subjects,id',
            'fullmark' => 'required|numeric',
            'note' => 'string|max:1550',
            'date' => 'required|date',
            'type' => 'string|in:test,exam'
        ]);

        $subject = Subject::find($validData['subject_id']);
        $class = $subject->school_class;
        $exam = Exam::create($validData);
        sendJson($exam);
    }

    public function storeExamProgramForClass(Request $request, $class_id)
    {
        $class = SchoolClass::find($class_id);
        if (!$class)
            sendMessageJson("Class not found", 404);

        $validData = $request->validate([
            'subjects' => 'required|array',
            'type' => 'required|in:exam,test'
        ]);
        $subjects = $validData['subjects'];
        $arr = [];
        foreach ($subjects as $s) {
            $validator = Validator::make($s, [
                'subject_id' => [
                    'required', Rule::exists('subjects', 'id')->where(function ($query) use ($class) {
                        return $query->where('class_id', $class->id);
                    })
                ],
                'fullmark' => 'required|numeric',
                'note' => 'string|max:1550',
                'date' => 'required|date',
            ]);
            $s['type'] = $validData['type'];
            if ($validator->fails())
                sendJson($validator->errors(), 422);
            array_push($arr, new Exam($s));
        }
        foreach ($arr as $e)
            $e->save();
        sendJson($class->exams);
    }

    public function update(Request $request, $id)
    {
        $exam = Exam::find($id);
        if (!$exam)
            sendMessageJson("Exam not found", 404);
        $validData = $request->validate([
            'subject_id' => 'exists:subjects,id',
            'fullmark' => 'numeric',
            'note' => 'string|max:1550',
            'date' => 'date',
            'type' => 'string|in:test,exam'
        ]);
        $exam->update($validData);
        $exam->save();
        sendJson($exam);
    }

    public function delete(Request $request, $id)
    {
        $exam = Exam::find($id);
        if (!$exam)
            sendMessageJson("Exam not found", 404);
        $exam->delete();
        sendMessageJson("The exam has been deleted successfly");
    }
}