<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\StudentAbsence;
use App\Models\TeacherAbsence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class AbsenceController extends Controller
{
    public function stuAbsence(Request $request)
    {
        $request->validate([
            'absence_ids' => 'required|array',
            'absence_ids.*' => 'integer'
        ]);

        foreach( $request->absence_ids as $abs_id)
        {
            $absence = new StudentAbsence();
            $absence->student_id = $abs_id;
            if($request->has('note')){
                $absence->note =$request->note;
            }
            $absence->save();
        }

        return response()->json([
            'message' => 'success',
            'status'=> 201
        ]);
    }

    public function teacherAbsence(Request $request)
    {
        $request->validate([
            'absence_ids' => 'required|array',
            //'absence_ids.*' => 'integer'
        ]);

        foreach($request->absence_ids as $abs_id)
        {
            $absence = new TeacherAbsence();
            $absence->teacher_id = $abs_id;
            if($request->has('note')){
                $absence->note =$request->note;
            }
            $absence->save();
        }

        return response()->json([
            'message' => 'success',
            'status'=> 201
        ]);

    }

    public function EditAbsence(Request $request)
    {
        if($request->for == 'student'){
            $abs_rec = StudentAbsence::where('student_id',$request->student_id)->whereDate('created_at',date($request->date))->first();
        }
        else {
            $abs_rec = TeacherAbsence::where('teacher_id',$request->teacher_id)->whereDate('created_at',date($request->date))->first();
        }
        if($abs_rec)
        {
            if($request->has('reasonable'))
            {
                $abs_rec->reasonable = $request->reasonable ;
            }
            if($request->has('note'))
            {
                $abs_rec->note = $request->note;
            }
            $abs_rec->save();

            return response()->json([$abs_rec]);
        }
        else
        {
            return response()->json(['message'=>'not found']);
        }
    }

    public function getAbsence(Request $request)
    {
        if($request->for == 'teacher')
        {
            if($request->has('start_date') && $request->has('end_date'))
            {
                $from = date($request->start_date);
                $to = date($request->end_date);
            }
            else if($request->has('end_date'))
            {
                $to = date($request->end_date);
                if($request->user()->role_id == RoleEnum::TEACHER->value)
                {
                    return TeacherAbsence::where('teacher_id', Auth::user()->teacher->id)->whereDate('created_at', '<', $to)->get();
                }
                else
                {
                    return TeacherAbsence::where('teacher_id', $request->teacher_id)->whereDate('created_at', '<', $to)->get();
                }
            }
            else if($request->has('start_date'))
            {
                $from = date($request->start_date);
                $to = date(Carbon::now());
            }
            else
            {
                if($request->user()->role_id == RoleEnum::TEACHER->value)
                {
                    return TeacherAbsence::where('teacher_id', Auth::user()->teacher->id)->get();
                }
                else
                {
                    return TeacherAbsence::where('teacher_id', $request->teacher_id)->get();
                }   
            }

            if($request->user()->role_id == RoleEnum::TEACHER->value)
            {
                return TeacherAbsence::where('teacher_id', Auth::user()->teacher->id)->whereBetween('created_at',[$from,$to])->get();
            }
            else
            {
                return TeacherAbsence::where('teacher_id', $request->teacher_id)->whereBetween('created_at',[$from,$to])->get();
            }
        }

        else if ($request->for == 'student')
        {
            if($request->has('start_date') && $request->has('end_date'))
            {
                $from = date($request->start_date);
                $to = date($request->end_date);
            }
            else if($request->has('end_date'))
            {
                $to = date($request->end_date);
                if($request->user()->role_id == RoleEnum::STUDENT->value)
                {
                    return StudentAbsence::where('student_id', Auth::user()->student->id)->whereDate('created_at', '<', $to)->get();
                }
                else
                {
                    return StudentAbsence::where('student_id', $request->student_id)->whereDate('created_at', '<', $to)->get();
                }
            }
            else if($request->has('start_date'))
            {
                $from = date($request->start_date);
                $to = date(Carbon::now());
            }
            else
            {
                if($request->user()->role_id == RoleEnum::STUDENT->value)
                {
                    return StudentAbsence::where('student_id', Auth::user()->student->id)->get();
                }
                else
                {
                    return StudentAbsence::where('student_id', $request->student_id)->get();
                }   
            }

            if($request->user()->role_id == RoleEnum::STUDENT->value)
            {
                return StudentAbsence::where('student_id', Auth::user()->student->id)->whereBetween('created_at',[$from,$to])->get();
            }
            else
            {
                return StudentAbsence::where('student_id', $request->student_id)->whereBetween('created_at',[$from,$to])->get();
            }

        }
        
    }

    public function getAbsenceSection(Request $request)
    {
        return DB::table('student_absence')
            ->join('student_records','Student_absence.student_id','=','student_records.student_id')
            ->where('student_records.section_id',$request->section_id)
            ->whereDate('student_absence.created_at','=',date($request->date))
            ->select('student_absence.*')
            ->get();
    }
}
