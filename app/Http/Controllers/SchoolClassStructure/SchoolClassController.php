<?php

namespace App\Http\Controllers\SchoolClassStructure;

use App\Http\Controllers\Controller;
use App\Models\SchoolClassStructure\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolClassController extends Controller
{

    public function index()
    {
        $classes = SchoolClass::all();
        sendJson($classes);
    }
    public function classesNames()
    {
        $classes = SchoolClass::all(['class_name']);
        sendJson(array_column($classes->toArray(), 'class_name'));
    }

    public function show($id)
    {
        $class = SchoolClass::find($id);
        if (!$class)
            sendMessageJson("Class not found", 404);
        $class->load('sections');
        $class->load('subjects');
        sendJson($class);
    }

    public function show_students($id) {
        $class = SchoolClass::find($id);
        if (!$class)
            sendMessageJson("Class not found", 404);
        sendJson($class->students);
    }

    public function create(Request $request)
    {
        $validData = $request->validate([
            'class_name' => 'required|string|unique:classes',
            'section_capacity' => 'required|numeric|min:1',
            'number_of_sessions' => 'required|numeric|min:1',
            'duration_per_session' => 'required|numeric|min:1',
            'break_after' => 'required|numeric|min:1',
            'break_duration' => 'required|numeric|min:1'
        ]);

        $class = SchoolClass::create($validData);

        sendJson($class);
    }

    public function update(Request $request, $id)
    {

        $class = SchoolClass::find($id);
        if (!$class)
            sendMessageJson("Class not found", 404);

        $validData = $request->validate([
            'class_name' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('classes')->ignore($class->id)
            ],
            'section_capacity' => 'sometimes|required|numeric|min:1',
            'number_of_sessions' => 'sometimes|required|numeric|min:1',
            'duration_per_session' => 'sometimes|required|numeric|min:1',
            'break_after' => 'sometimes|required|numeric|min:1',
            'break_duration' => 'sometimes|required|numeric|min:1'
        ]);

        $class->update($validData);
        $class->save();

        sendJson($class);
    }

    public function delete(Request $request, $id)
    {

        $class = SchoolClass::find($id);
        if (!$class)
            sendMessageJson("Class not found", 404);

        $class->delete();
        sendMessageJson("Class has been deleted successfly");
    }


}