<?php

namespace App\Http\Controllers\SchoolClassStructure;

use App\Http\Controllers\Controller;
use App\Models\SchoolClassStructure\ClassSection;
use App\Models\SchoolClassStructure\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassSectionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $sections = ClassSection::
            with('school_class')
            ->searchable($request)
            ->searchableWith($request, 'school_class')
            ->orderable($request)
            ->paginate($perPage);

        sendJson($sections);
    }

    public function create(Request $request, $class_id)
    {
        $class = SchoolClass::find($class_id);
        if (!$class)
            sendMessageJson("Class not found", 404);
        $validData = $request->validate([
            'prompt_id' => 'exists:prompts,id',
            'number' => [
                'required',
                'numeric', Rule::unique('class_sections')->where(function ($query) use ($request, $class_id) {
                    return $query->where('number', $request->number)
                        ->where('class_id', $class_id);
                })
            ],
            'capacity' => 'numeric',
        ]);

        $validData['class_id'] = $class_id;

        $section = ClassSection::create($validData);
        sendJson($section);
    }

    public function update(Request $request, $class_id, $id)
    {
        $class = SchoolClass::find($class_id);
        if (!$class)
            sendMessageJson("Class not found", 404);
        $section = ClassSection::find($id);
        if (!$section)
            sendMessageJson("Class Section not found", 404);

        $validData = $request->validate([
            'prompt_id' => 'exists:prompts,id',
            'number' => [
                'required',
                'numeric', Rule::unique('class_sections')->where(function ($query) use ($request, $class_id) {
                    return $query->where('number', $request->number)
                        ->where('class_id', $class_id);
                })
            ],
            'capacity' => 'numeric',
        ]);



        $section->update($validData);
        $section->save();
        sendJson($section);
    }

    public function delete( $class_id, $id)
    {
        $class = SchoolClass::find($class_id);
        if (!$class)
            sendMessageJson("Class not found", 404);
        $section = ClassSection::find($id);
        if (!$section)
            sendMessageJson("Class Section not found", 404);

        $section->delete();
        sendMessageJson("section has been deleted successfly");
    }
}