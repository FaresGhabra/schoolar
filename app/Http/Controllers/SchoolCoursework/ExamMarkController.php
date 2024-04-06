<?php

namespace App\Http\Controllers\SchoolCoursework;

use App\Http\Controllers\Controller;
use App\Models\SchoolAccounts\Student;
use App\Models\SchoolClassStructure\ClassSection;
use App\Models\SchoolClassStructure\SchoolClass;
use App\Models\SchoolCoursework\Exam;
use App\Models\SchoolCoursework\ExamMark;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ExamMarkController extends Controller
{

    public function getClassMarks(Request $request, $id, $class_id)
    {
        $class = SchoolClass::find($class_id);
        if (!$class)
            sendMessageJson("Class not found", 404);
        $exam = Exam::find($id);
        if (!$exam)
            sendMessageJson("Exam not found", 404);
        if ($exam->subject->class_id != (int) $class_id)
            sendMessageJson("The class doesn't have such exam", 422);

        $marks = ExamMark::whereHas('exam', function ($q) use ($id) {
            $q->where('id', $id);
        })->whereHas('student.section.school_class', function ($q) use ($class_id) {
            $q->where('id', $class_id);
        })->get();
        sendJson($marks);
    }

    public function getExamMarks(Request $request, $id)
    {
        $exam = Exam::find($id);
        if (!$exam)
            sendMessageJson("Exam not found", 404);
        $exam->load('marks');
        sendJson($exam);
    }

    public function getSectionMarks(Request $request, $id, $section_id)
    {
        $exam = Exam::find($id);
        if (!$exam)
            sendMessageJson("Exam not found", 404);

        $section = ClassSection::find($section_id);
        if (!$section)
            sendMessageJson("Section not found", 404);

        $marks = ExamMark::whereHas('exam', function ($q) use ($id) {
            $q->where('id', $id);
        })->whereHas('student.section', function ($q) use ($section_id) {
            $q->where('id', $section_id);
        })->get();

        sendJson($marks);
    }

    public function storeMarksForSection(Request $request, $section_id)
    {
        $section = ClassSection::find($section_id);
        if (!$section)
            sendMessageJson("Section not found", 404);

        $validData = $request->validate([
            'exam_id' => [
                'required',
                'exists:exams,id,deleted_at,NULL',
                function ($attribute, $value, $fail) use ($request, $section) {
                    $e = Exam::find($request->exam_id);
                    if ($e && $e->subject->class_id != $section->class_id) {
                        return $fail('Section class does not match exam subject class');
                    }
                }

            ],
            'students' => 'required|array'
        ]);
        $exam = Exam::find($validData['exam_id']);
        if (new \DateTime($exam->date) > now())
            sendMessageJson("Exam is not finished yet");
        $arr = [];
        foreach ($validData['students'] as $student) {
            $validator = Validator::make($student, [
                'student_id' => [
                    'required',
                    Rule::exists('students', 'id')->where(function ($query) use ($section) {
                        return $query->where('section_id', $section->id);
                    }),
                ],
                'mark' => "required|integer|max:{$exam->fullmark}",
                'note' => 'string|max:1550'
            ]);
            if ($validator->fails())
                sendJson($validator->errors(), 422);
            $student['exam_id'] = $validData['exam_id'];

            array_push($arr, $student);
        }
        $marks = [];
        foreach ($arr as $student) {

            $mark = ExamMark::where('student_id', $student['student_id'])->where('exam_id', $exam->id)->first();
            if ($mark)
                $mark->update($student);
            else
                $mark = ExamMark::create($student);
            array_push($marks, $mark);
        }
        sendJson($marks);
    }

    public function storeMarkForStudent(Request $request, $student_id)
    {
        $student = Student::find($student_id);
        if (!$student)
            sendMessageJson("Student not found", 404);

        $exam = Exam::find($request->exam_id);

        if (!$exam)
            sendMessageJson("Exam not found", 404);
        if (new \DateTime($exam->date) > now())
            sendMessageJson("Exam is not finished yet");

        $validData = $request->validate([
            'exam_id' => [
                function ($attribute, $value, $fail) use ($exam, $student) {
                    $e = $exam;
                    if ($e && $e->subject->class_id != $student->section->class_id) {
                        return $fail('Student class does not match exam subject class');
                    }
                }

            ],
            'mark' => "required|integer|max:{$exam->fullmark}",
            'note' => 'string|max:1550'
        ]);
        $validData['student_id'] = $student_id;
        $mark = ExamMark::where('student_id', $student_id)->where('exam_id', $exam->id)->first();
        if ($mark)
            $mark->update($validData);
        else
            $mark = ExamMark::create($validData);
        sendJson($mark);
    }

    public function getUnfinishedExams()
    {
        $exams = Exam::where('date', '>', now())->get();
        sendJson($exams);
    }

    public function getFinishedExams()
    {
        $exams = Exam::where('date', '<', now())->get();
        sendJson($exams);
    }

    public function getUndoneExams(Request $request)
    {
        $exams = Exam::where('date', '<', now())->get();
        $result = [];
        foreach ($exams as $exam) {
            if ($exam->students()->whereDoesntHave('exam_marks')->get())
                array_push($result, $exam);
        }
        sendJson($exams);
    }

    public function getUndoneMarks(Request $request, $id)
    {
        $exam = Exam::find($id);

        if (!$exam)
            sendMessageJson("Exam not found", 404);
        if (new \DateTime($exam->date) > now())
            sendMessageJson("Exam is not finished yet");
        $students = $exam->students()->whereDoesntHave('exam_marks')->get();
        sendJson($students);
    }

    public function getStudentMark(Request $request, $exam_id, $student_id)
    {
        $exam = Exam::find($exam_id);
        if (!$exam)
            sendMessageJson("Exam not found", 404);
        $student = Student::find($student_id);
        if (!$student)
            sendMessageJson("Student not found", 404);
        $mark = ExamMark::where('student_id', $student->id)->where('exam_id', $exam->id)->get();
        $mark->load('exam.subject');
        sendJson($mark);
    }

    public function getStudentMarks(Request $request, $student_id)
    {
        $student = Student::find($student_id);
        if (!$student)
            sendMessageJson("Student not found", 404);
        $student->load('exam_marks.exam.subject');
        sendJson($student->exam_marks);
    }
}