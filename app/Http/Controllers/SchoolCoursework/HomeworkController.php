<?php

namespace App\Http\Controllers\SchoolCoursework;

use App\Http\Controllers\Controller;
use App\Models\SchoolClassStructure\ClassSection;
use App\Models\SchoolCoursework\Homework;
use App\Models\SchoolCoursework\SectionHomework;
use Illuminate\Http\Request;

class HomeworkController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $homeworks = Homework
            ::with('teacher')
            ->searchable($request)
            ->searchableWith($request, 'teacher')
            ->paginate($perPage);
        sendJson($homeworks);
    }

    public function show(Request $request, $id)
    {
        $homework = Homework::find($id);
        if (!$homework)
            sendMessageJson("Homework not found", 404);

        // $this->authorize($homework, $request->user());
        $homework->load('teacher');
        $homework->teacher->makeHidden('user');
        sendJson($homework);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $user->load('teacher');
        $validData = $request->validate([
            'title' => 'required|string|max:255',
            'note' => 'string|max:1500',
            'fullmark' => 'required|numeric',
            'date' => 'required|date',
            'sections' => 'required|array',
            'sections.*' => 'exists:class_sections,id'
        ]);
        $validData['teacher_id'] = $user->teacher->id;
        $homework = Homework::create($validData);
        $homework->sections()->attach($validData['sections']);
        $homework->load('sections');
        sendJson($homework);
    }

    public function update(Request $request, $id)
    {
        $homework = Homework::find($id);
        if (!$homework)
            sendMessageJson("Homework not found", 404);
        $validData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'note' => 'string|max:1500',
            'fullmark' => 'sometimes|required|numeric',
            'date' => 'sometimes|required|date',
            'sections' => 'sometimes|required|array',
            'sections.*' => 'exists:class_sections,id'
        ]);
        if (isset($validData['sections'])) {
            $homework->sections()->sync($validData['sections']);
            unset($validData['sections']);
        }
        $homework->update($validData);
        $homework->save();
        $homework->load('sections');
        sendJson($homework);
    }


    public function showSectionAllHomworks(Request $request, $section_id)
    {
        $section = ClassSection::find($section_id);
        if (!$section)
            sendMessageJson('Section not found', 404);
        $homeworks = $section->homeworks;
        foreach ($homeworks as $h)
            $h->teacher->makeHidden('user');
        sendJson($homeworks);
    }


    public function showSectionNewHomworks(Request $request, $section_id)
    {
        $section = ClassSection::find($section_id);
        if (!$section)
            sendMessageJson("Section not found", 404);
        $homeworks = $section->new_homeworks;
        foreach ($homeworks as $h)
            $h->teacher->makeHidden('user');
        sendJson($homeworks);
    }

    public function showSectionOldHomworks(Request $request, $section_id)
    {
        $section = ClassSection::find($section_id);
        if (!$section)
            sendMessageJson("Section not found", 404);
        $homeworks = $section->old_homeworks;
        foreach ($homeworks as $h)
            $h->teacher->makeHidden('user');
        sendJson($homeworks);
    }

    public function showTeacherAllHomeworks(Request $request)
    {
        $user = $request->user();
        sendJson($user->teacher->homeworks);
    }

    public function showTeacherNewHomeworks(Request $request)
    {
        $user = $request->user();
        sendJson($user->teacher->new_homeworks);
    }

    public function showTeacherUndoneHomework(Request $request)
    {
        $user = $request->user();
        sendJson($user->teacher->undone_homeworks);
    }

    public function showTeacherOldHomeworks(Request $request)
    {
        $user = $request->user();
        sendJson($user->teacher->old_homeworks);
    }


    public function delete(Request $request, $id)
    {
        $homework = Homework::find($id);
        if (!$homework)
            sendMessageJson("Homework not found", 404);

        $homework->delete();
        sendMessageJson("Homework has been deleted successfly");
    }
}