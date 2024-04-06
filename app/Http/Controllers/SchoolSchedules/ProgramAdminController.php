<?php

namespace App\Http\Controllers\SchoolSchedules;

use App\Http\Controllers\Controller;
use App\Models\SchoolAccounts\Teacher;
use App\Models\SchoolClassStructure\ClassSection;
use App\Models\SchoolSchedules\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProgramAdminController extends Controller
{
    public function showSectionProgram(Request $request, $section_id)
    {
        $section = ClassSection::find($section_id);
        if (!$section)
            sendMessageJson('Section not found', 404);
        sendJson($section->detailed_program);
    }

    public function updateSection(Request $request, $section_id)
    {
        $section = ClassSection::find($section_id);
        if (!$section)
            sendMessageJson('Section not found', 404);
        $data = $request->program;
        if (!$data || sizeof($data) !== 7)
            sendMessageJson('Data input is invalid: program is required', 422);

        $sessions = [];
        foreach ($data as $day => $s) {

            if (!is_array($s))
                continue;
            if (sizeof($s) !== $section->school_class->number_of_sessions)
                sendMessageJson("Number of sessions don't match the number of sessions of this section", 422);

            foreach ($s as $k => $se) {
                $record = $se;
                $record['section_id'] = $section->id;
                $record['session'] = $k + 1;
                $record['day'] = $day + 1;

                $validator = Validator::make($record, [
                    'teacher_id' => 'exists:teachers,id',
                    'subject_id' => [
                        Rule::exists('subjects', 'id')->where(function ($query) use ($record, $section) {
                            return $query->where('class_id', $section->class_id);
                        })
                    ],
                    'session' => "required|numeric|min:1|max:{$section->school_class->number_of_sessions}",
                    'day' => 'required|numeric|min:1|max:7'
                ]);

                if ($validator->fails())
                    sendJson($validator->errors(), 422);
                array_push($sessions, $record);
            }
        }

        foreach ($sessions as $session) {
            Session::updateOrInsert(
                ['day' => $session['day'], 'session' => $session['session']],
                $session
            );
        }

        sendJson($section->detailed_program);
    }

    public function update(Request $request, $section_id, $day_number, $session_number)
    {

        $section = ClassSection::find($section_id);
        if (!$section)
            sendMessageJson('Section not found', 404);
        $validData = $request->validate([
            'teacher_id' => 'exists:teachers,id',
            'subject_id' => 'exists:subjects,id',
        ]);
        if ($session_number < 1 || $session_number > $section->school_class->number_of_sessions)
            sendMessageJson("Invalid session", 422);
        if ($day_number < 1 || $day_number > 7)
            sendMessageJson("Invalid day", 422);
        $validData['day'] = (int) $day_number;
        $validData['session'] = (int) $session_number;
        $validData['section_id'] = $section->id;
        Session::updateOrInsert(
            ['day' => $day_number, 'session' => $session_number],
            $validData
        );
        $session = Session::where('session', $session_number)->where('day', $day_number)->get();
        sendJson($session);
    }

    public function showTeacherProgram(Request $request, $teacher_id)
    {
        $teacher = Teacher::find($teacher_id);
        if (!$teacher)
            sendMessageJson('Teacher not found', 404);
        sendJson($teacher->detailed_program);
    }
}