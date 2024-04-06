<?php

namespace App\Http\Controllers\SchoolResources;

use App\Http\Controllers\Controller;
use App\Models\SchoolCurriculum\Subject;
use App\Models\SchoolResources\Resource;
use App\Models\TeacherSectionsSubjects;
use Illuminate\Http\Request;

class ResourceAdminController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $resources = Resource::
            with(['teacher', 'subject', 'school_class'])
            ->searchable($request)
            ->searchableWith($request, 'teacher')
            ->searchableWith($request, 'subject')
            ->searchableWith($request, 'school_class')
            ->paginate($perPage);
        sendJson($resources);
    }

    public function show(Request $request, $id)
    {
        $resource = Resource::find($id);
        if (!$resource)
            sendMessageJson('Resource not found', 404);
        $resource->load(['teacher', 'subject', 'school_class']);
        sendJson($resource);
    }

    public function store(Request $request)
    {
        $teacher = $request->user();

        $validData = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'url' => 'url',
            'description' => 'string',
            'r_file' => 'file|mimes:jpeg,png,jpg,gif,pdf,txt',
        ]);
        $record = TeacherSectionsSubjects::where("teacher_id", $teacher->teacher->id)->where("subject_id", $validData['subject_id'])->get();
        if (empty($record->toArray())) sendMessageJson("Teacher is not allowed to add to this subject", 403);
        
        $validData['user_id'] = $teacher->id;
        if (isset($validData['r_file'])) {
            $validData['file'] = $validData['r_file']->store('resources_files', 'public');
        }
        $resource = Resource::create($validData);
        sendJson($resource);
    }

    public function update(Request $request, $id)
    {
        $resource = Resource::find($id);
        if (!$resource)
            sendMessageJson('Resource not found', 404);
        $validData = $request->validate([
            'subject_id' => 'exists:subjects,id',
            'r_file' => 'file|mimes:jpeg,png,jpg,gif,pdf,txt',
            'url' => 'url',
            'description' => 'string'
        ]);
        if (isset($validData['r_file'])) {
            if ($resource->file && file_exists(storage_path('app/public/' . $resource->file)))
                unlink(storage_path('app/public/' . $resource->file));
            $validData['file'] = $validData['r_file']->store('resources_files', 'public');
        }
        $resource->update($validData);
        $resource->save();
        sendJson($resource);
    }

    public function delete(Request $request, $id)
    {
        $resource = Resource::find($id);
        if (!$resource)
            sendMessageJson('Resource not found', 404);
        $resource->delete();
        sendMessageJson('Resource has been deleted successfly');
    }
}