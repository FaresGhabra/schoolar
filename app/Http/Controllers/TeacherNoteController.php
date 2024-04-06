<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Auth;
use App\Models\HelperMethods;


use App\Models\TeacherNote;
use Illuminate\Http\Request;

class TeacherNoteController extends Controller
{
    public function addNote(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'lesson_id' => 'required|integer',
            'note' => 'required|string'
        ]);

        $note = new TeacherNote();
        $note->student_id = $request->student_id;
        $note->lesson_id  = $request->lesson_id ;
        $note->teacher_id = Auth::user()->teacher->id;
        $note->note = $request->note;
        $note->save();

        sendJson($note);
    }

    public function getNotes(Request $request)
    {
        if(Auth::user()->role_id == RoleEnum::STUDENT)
        {
            sendJson(TeacherNote::where('student_id',Auth::user()->student->id)->searchable($request))->get();
        }
        else
        {
            sendJson(TeacherNote::where('student_id',$request->student_id)->searchable($request))->get();
        }
    }
}
