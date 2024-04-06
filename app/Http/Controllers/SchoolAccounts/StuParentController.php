<?php

namespace App\Http\Controllers\SchoolAccounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Models\SchoolAccounts\User;
use App\Models\SchoolAccounts\StuParent;
use App\Enums\RoleEnum;
use App\Enums\GenderEnum;

class StuParentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $parents = User
            ::with('student_parent')
            ->whereHas('student_parent')
            ->searchable($request)
            ->orderable($request)
            ->paginate($perPage);

        return response()->json($parents);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user)
            sendMessageJson("User not found", 404);

        $parent = $user->student_parent;
        if (!$parent)
            sendMessageJson('Parent not found', 404);

        $this->authorize('view', $parent);

        return sendJson($user);
    }

    public function showChildren(Request $request)
    {
        $user = $request->user();
        if (!$user->student_parent)
            sendMessageJson("Unauthorized.", 401);

        $children = $user->student_parent->students;
        sendJson($children);
    }

    public function create(Request $request)
    {
        $this->authorize('create', StuParent::class);
        $validData = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'fullname' => 'required|string|max:255',
            'phone_number' => 'required|numeric|digits:10|unique:users',
            'gender' => 'required|in:male,female',
            'address' => 'required|string',
        ]);

        $validData['role_id'] = RoleEnum::PARENT->value;
        $user = User::create($validData);
        $validData['user_id'] = $user->id;
        $validData['active'] = 1;
        StuParent::create($validData);

        $user->load('student_parent');

        sendJson($user);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', StuParent::class);

        $user = User::find($id);
        if (!$user) sendMessageJson("User not found", 404);

        $parent = $user->student_parent;
        if (!$parent) sendMessageJson("Parent not found", 404);

        $validData = $request->validate([
            'parent_id' => 'sometimes|required|exists:parents,id',
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
            'password' => 'sometimes|required|string|min:8',
            'birth_date' => 'date',
            'fullname' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string',
        ]);

        $parent->update($validData);
        $user->update($validData);
        sendJson($user);
    }

}