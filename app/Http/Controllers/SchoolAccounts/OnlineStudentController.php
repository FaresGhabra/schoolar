<?php

namespace App\Http\Controllers\SchoolAccounts;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\SchoolAccounts\OnlineStudent;
use App\Models\SchoolAccounts\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnlineStudentController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); 

        $users = User::with('online_student')
            ->orderable($request)
            ->searchable($request)
            ->orWhereHas(
                'online_student',
                function ($query) use ($request) {
                    $query->searchable($request)->orderable($request);
                }
            )
            ->paginate($perPage);

        sendJson($users);
    }

    public function show(Request $request, $id = null)
    {
        $user = $request->user();
        if ($id === null) {
            $online_student = $user->online_student;
        } else {
            $user = User::find($id);
            $online_student = $user->online_student;
        }

        if (!$online_student)
            sendMessageJson("User profile is not found", 404);

        $this->authorize('view', $online_student);

        sendJson($user);
    }

    public function register(Request $request)
    {
        $validData = $request->validate([
            'fullname' => 'required|string|max:255',
            'gender' => 'required',
            'username' => 'required|min:8|unique:users',
            'phone_number' => 'required|numeric|digits:10|unique:users',
            'birth_date' => 'required|date',
            'study_year' => 'required|min:1|max:13',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'address' => 'sometimes|required|string',
        ]);

        $validData['role_id'] = RoleEnum::ONLINE_STUDENT->value;

        $user = User::create($validData);

        $validData['user_id'] = $user->id;

        OnlineStudent::create($validData);

        $token = $user->createToken('api_token')->plainTextToken;

        $user->load('online_student');

        sendJson([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function update(Request $request, $id = null)
    {
        $user = $request->user();

        if ($id === null) {
            $online_student = $user->online_student;
        } else {
            $user = User::find($id);
            $online_student = $user->online_student;
        }

        if (!$online_student)
            sendMessageJson("User profile is not found", 404);

        $this->authorize('update', $online_student);

        if (!$online_student)
            sendMessageJson("User not fount", 404);

        $validData = $request->validate([
            'fullname' => 'sometimes|required|string|max:255',
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
            'birth_date' => 'sometimes|required|date',
            'study_year' => 'sometimes|required|numeric|between:1,13',
            'address' => 'sometimes|required|string',
            'password' => 'sometimes|required|string|min:8'
        ]);

        $user->update($validData);
        $user->save();
        $online_student->update($validData);
        $online_student->save();
        sendJson($user);
    }

    public function destroy(Request $request, $id = null)
    {
        $user = $request->user();

        if ($id === null) {
            $online_student = $user->online_student;
        } else {
            $user = User::find($id);
            if (!$user)
                sendMessageJson("User profile is not found", 404);
            $online_student = $user->online_student;
        }

        if (!$online_student)
            sendMessageJson("User profile is not found", 404);

        $this->authorize('delete', $online_student);

        $online_student->delete();
        $user->delete();
        $message = "The account of '{$user->fullname}' has been deleted successfully";
        sendMessageJson($message);
    }
}