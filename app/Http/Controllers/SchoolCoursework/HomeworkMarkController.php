<?php

namespace App\Http\Controllers\SchoolCoursework;

use App\Http\Controllers\Controller;
use App\Models\SchoolAccounts\Student;
use App\Models\SchoolClassStructure\ClassSection;
use App\Models\SchoolClassStructure\SchoolClass;
use App\Models\SchoolCoursework\Homework;
use App\Models\SchoolCoursework\HomeworkMark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HomeworkMarkController extends Controller
{

    public function storeMarksForSection(Request $request, $section_id)
    {
        $section = ClassSection::find($section_id);
        if (!$section)
            sendMessageJson("Section not found", 404);

        $validData = $request->validate([
            'homework_id' => [
                'required',
                'exists:homeworks,id,deleted_at,NULL',
            ],
            'students' => 'required|array'
        ]);
        $homework = Homework::find($validData['homework_id']);
        if (new \DateTime($homework->date) > now())
            sendMessageJson("Homework is not finished yet");
        if ($homework->teacher)
            $arr = [];
        foreach ($validData['students'] as $student) {
            $validator = Validator::make($student, [
                'student_id' => [
                    'required',
                    Rule::exists('students', 'id')->where(function ($query) use ($section) {
                        return $query->where('section_id', $section->id);
                    }),
                ],
                'mark' => "required|integer|max:{$homework->fullmark}",
                'note' => 'string|max:1550'
            ]);
            if ($validator->fails())
                sendJson($validator->errors(), 422);
            $student['exam_id'] = $validData['exam_id'];

            array_push($arr, $student);
        }
        $marks = [];
        foreach ($arr as $student) {

            $mark = HomeworkMark::where('student_id', $student['student_id'])->where('homework_Id', $homework->id)->first();
            if ($mark)
                $mark->update($student);
            else
                $mark = HomeworkMark::create($student);
            array_push($marks, $mark);
        }
        sendJson($marks);
    }

    public function storeMarkForStudent(Request $request, $student_id)
    {
        $stu_mark = HomeworkMark::where('student_id',$student_id)->where('homwork_id',$request->homwork_id)->first();
        if($stu_mark)
        {
            $stu_mark->update([
                'mark'=>$request->mark
            ]);
        }
        else
        {
            $stu_mark = new HomeworkMark();
            $stu_mark->homework_id = $request->homework_id;
            $stu_mark->student_id = $student_id;
            $stu_mark->mark = $request->mark;
            if($request->has('note')){
                $stu_mark->note = $request->note;
            }
            $stu_mark->save();
        }
        sendJson($stu_mark);
    }

    public function getHomeworkMarks(Request $request, $id)
    {
        $homework = Homework::find($id);
        if (!$homework)
            sendMessageJson("homework not found", 404);
        $homework->load('marks');
        sendJson($homework);
    }


    public function getStudentMarks(Request $request, $student_id)
    {
        $marks = HomeworkMark::where('student_id',$student_id)->pageinat(10);
        sendJson($marks);
    }

}