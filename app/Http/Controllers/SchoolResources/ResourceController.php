<?php

namespace App\Http\Controllers\SchoolResources;

use App\Http\Controllers\Controller;
use App\Models\SchoolCurriculum\Subject;
use App\Models\SchoolResources\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $resources = Resource::
            with(['teacher', 'subject', 'school_class'])
            ->searchable($request)
            ->searchableWith($request, 'teacher', ['fullname'])
            ->searchableWith($request, 'subject')
            ->searchableWith($request, 'school_class')
            ->paginate($perPage);
        $resources = $resources->toArray();
        foreach ($resources['data'] as $k => $v) {
            $resources['data'][$k]['teacher'] = $resources['data'][$k]['teacher']['fullname'];
        }
        sendJson($resources);
    }

    public function show(Request $request, $id)
    {
        $resource = Resource::find($id);
        if (!$resource)
            sendMessageJson('Resource not found', 404);
        $resource->load(['teacher', 'subject', 'school_class']);
        $resource = $resource->toArray();
        $resource['teacher'] = $resource['teacher']['fullname'];
        sendJson($resource);
    }

    public function showClassSubject(Request $request, $subject_id) {
        $subject = Subject::find($subject_id);
        if (!$subject) sendMessageJson("Subject not found", 404);
        sendJson($subject->resources);
    }

}