<?php

namespace App\Http\Controllers\SchoolCurriculum;

use App\Http\Controllers\Controller;
use App\Models\SchoolClassStructure\SchoolClass;
use App\Models\SchoolCurriculum\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $subjects = Subject::with('school_class')->searchable($request)->searchableWith($request, 'school_class')->paginate($perPage);
        sendJson($subjects);
    }

    public function class_subjects(Request $request, $class_id)
    {
        $class = SchoolClass::find($class_id);
        if (!$class)
            sendMessageJson("Class not found", 404);
        sendJson($class->subjects);
    }

    public function create(Request $request)
    {
        if ($request->has('class_id'))
            $validData = $request->validate([
                'class_id' => 'exists:classes,id',
                'name' => [
                    'required',
                    
                    'string', Rule::unique('subjects')->where(function ($query) use ($request) {
                        return $query->where('name', $request->name)
                            ->where('class_id', $request->class_id);
                    })
                ]
            ]);
        else
            $validData = $request->validate(['name' => 'required|unique:subjects']);

        if (isset($validData['class_id']))
            sendJson(Subject::create($validData));
        else {
            $classes = SchoolClass::all();
            $subjects = [];
            foreach ($classes as $class) {
                array_push($subjects, $class->subjects()->create($validData));
            }
            sendJson($subjects);
        }
    }

    public function update(Request $request, $id = null)
    {
        if (!$id) {
            $validData = $request->validate(['old_name' => 'required|exists:subjects,name', 'name' => 'required|unique:subjects']);
            $subjects = Subject::where('name', $validData['old_name'])->update($request->only('name'));
            $subjects = Subject::where('name', $validData['name'])->get();
            sendJson($subjects);
        } else {
            $subject = Subject::find($id);
            if (!$subject)
                sendMessageJson("Subject not found", 404);
            $validData = $request->validate([
                'name' => [
                    'string', Rule::unique('subjects')->where(function ($query) use ($request, $subject) {
                        return $query->where('name', $request->name)
                            ->where('class_id', $subject->class_id);
                    })
                ]
            ]);
            $subject->update($validData);
            sendJson($subject);
        }
    }

    public function delete(Request $request, $id = null)
    {
        if (!$id) {
            $validData = $request->validate(['name' => 'required|exists:subjects']);
            Subject::where('name', $validData['name'])->delete();
            sendMessageJson("Subjects has been deleted successfly");
        } else {
            $subject = Subject::find($id);
            if (!$subject)
                sendMessageJson("Subject not found", 404);
            $subject->delete();
            sendMessageJson("Subject has been deleted successfly");
        }
    }
}