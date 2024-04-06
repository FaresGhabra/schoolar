<?php

namespace App\Http\Controllers\SchoolAccounts;

use App\Http\Controllers\Controller;
use App\Models\SchoolClassStructure\ClassSection;
use App\Models\SchoolCurriculum\Subject;
use App\Models\TeacherSectionsSubjects;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\RoleEnum;
use App\Models\SchoolAccounts\User;
use App\Models\SchoolAccounts\Prompt;
use App\Models\SchoolAccounts\Teacher;
use App\Models\SchoolAccounts\Admin;

class SchoolStaffController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Items per page
        $role = $request->query('role');
        $roles = ['admin', 'teacher', 'prompt'];

        if ($role !== null && !in_array($role, $roles))
            $role = 'teacher';

        if (!$role)
            $users = User::with('admin')->with('teacher')->with('prompt')
                ->whereHas('prompt')->whereHas('admin')->whereHas('teacher');
        else
            $users = User::with($role)->whereHas($role);

        $users = $users->searchable($request)
            ->orderable($request)
            ->paginate($perPage);

        sendJson($users);
    }

    public function create(Request $request)
    {
        $roles = [RoleEnum::ADMIN->value, RoleEnum::TEACHER->value, RoleEnum::PROMPT->value];

        $validData = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|numeric|digits:10|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'fullname' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'role_id' => 'required|in:' . implode(",", $roles),
            'address' => 'required|string'
        ]);

        $user = User::create($validData);
        $validData['user_id'] = $user->id;
        if ($request->role_id == RoleEnum::TEACHER->value) {
            Teacher::create($validData);
            $user->load('teacher');
        } else if ($request->role_id == RoleEnum::PROMPT->value) {
            Prompt::create($validData);
            $user->load('prompt');
        } else if ($request->role_id == RoleEnum::ADMIN->value) {
            Admin::create($validData);
            $user->load('admin');
        }

        sendJson($user);
    }

    public function show($id)
    {
        $user = User::find($id);
        if ($user->role_id == RoleEnum::ADMIN->value)
            $user->load('admin');
        else if ($user->role_id == RoleEnum::TEACHER->value)
            $user->load('teacher');
        else if ($user->role_id == RoleEnum::PROMPT->value)
            $user->load('prompt');
        else
            sendMessageJson("User is not found", 404);
        sendJson($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $roles = [RoleEnum::ADMIN->value, RoleEnum::PROMPT->value, RoleEnum::TEACHER->value];

        if ($user->role_id == RoleEnum::ADMIN->value)
            $user->load('admin');
        else if ($user->role_id == RoleEnum::TEACHER->value)
            $user->load('teacher');
        else if ($user->role_id == RoleEnum::PROMPT->value)
            $user->load('prompt');
        else
            sendMessageJson("User is not found", 404);

        $validData = $request->validate([
            'username' => [
                'sometimes',
                'required',
                'min:8',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone_number' => [
                'sometimes',
                'required',
                'numeric',
                'digits:10',
                Rule::unique('users')->ignore($user->id)
            ],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'sometimes|required|string|confirmed|min:8',
            'fullname' => 'sometimes|required|string|max:255',
            'gender' => 'sometimes|required|in:male,female',
            'role_id' => 'sometimes|required|in:' . implode(",", $roles),
            'address' => 'sometimes|required|string'
        ]);

        $user->update($validData);

        sendJson($user);
    }

    public function assignTeacher(Request $request, $teacher_id)
    {
        $teacher = Teacher::find($teacher_id);
        if (!$teacher)
            sendMessageJson("Teacher not found", 404);
        $validData = $request->validate([
            'a' => 'required|array',
            'a.*.section_id' => 'required|exists:class_sections,id,deleted_at,NULL',
            'a.*.subject_id' => 'required|exists:subjects,id,deleted_at,NULL',
        ]);

        foreach ($validData['a'] as $a) {
            $section = ClassSection::find($a['section_id']);
            $subject = Subject::find($a['subject_id']);
            if ($section->class_id != $subject->class_id)
                sendMessageJson("The subject [$subject->id] doesn't belong to section [$section->id]", 422);
        }
        $res = [];
        foreach ($validData['a'] as $a) {
            $a['teacher_id'] = $teacher->id;
            $item = TeacherSectionsSubjects::updateOrInsert($a);
            array_push($res, $a);
        }
        sendJson($res);
    }


}