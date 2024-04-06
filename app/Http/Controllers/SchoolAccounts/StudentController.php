<?php


namespace App\Http\Controllers\SchoolAccounts;

use App\Http\Controllers\Controller;
use App\Models\SchoolAccounts\StuParent;
use App\Models\SchoolAccounts\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use App\Models\SchoolAccounts\Student;
use App\Enums\RoleEnum;
use App\Enums\GenderEnum;
use App\Models\StudentRecord;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $students = Student::
            searchable($request)
            ->orderable($request)
            ->with([
                'user' => function ($query) use ($request) {
                    return $query->searchable($request)->orderable($request);
                }
            ])
            ->paginate($perPage);
        return response()->json($students);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user)
            sendMessageJson("User not found", 404);

        $student = $user->student;
        if (!$student)
            sendMessageJson('Student not found', 404);


        $this->authorize('view', $student);

        return sendJson($user);
    }



    public function create(Request $request)
    {

        // $this->authorize('create');

        $validData = $request->validate([
            'parent_id' => 'required|exists:parents,id',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'birth_date' => 'date',
            'fullname' => 'required|string|max:255',
            'gender' => [new Enum(GenderEnum::class)],
            'address' => 'required|string',
        ]);

        $validData['role_id'] = RoleEnum::STUDENT;
        $validData['active'] = 1;

        $parent = StuParent::find($request->parent_id);

        $user = User::create($validData);

        $validData['user_id'] = $user->id;
        $validData['parent_id'] = $parent->id;

        Student::create($validData);

        $user->load('student')->makeHidden('user');

        sendJson($user);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update');

        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $validData = $request->validate([
            'parent_id' => 'required|exists:parents,id',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'birth_date' => 'date',
            'fullname' => 'required|string|max:255',
            'gender' => [new Enum(GenderEnum::class)],
            'address' => 'required|string',
        ]);

        $user = $student->user;
        $student->updateFromArrayAndSave($validData);
        $user->updateFromArrayAndSave($validData);
        sendJson($user);
    }

    public function deactive(Request $request, $id)
    {
        $this->authorize('deactive');
        $student = Student::find($id);
        if (!$student) {
            sendMessageJson('Student not found', 404);
        }

        $record = new StudentRecord();
        $record->user_id = $request->user_id;
        $record->status = 'deactive';
        $student->active = false;
        $student->section_id = null;
        $student->save();
        $record->save();

        sendMessageJson("Student has been deactivated");
    }

    public function studentRecord(Request $request)
    {
        $validData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:class_sections,id',
        ]);

        $student = Student::find($validData['student_id']);

        $record = new StudentRecord();
        $record->student_id = $student->id;
        $record->section_id = $request->section_id;
        $record->status = 'active';
        $record->expire_date = date("Y-m-d H:i:s", time() + 365 * 24 * 60 * 60);

        $student->section_id = $validData['section_id'];

        $record->save();
        $student->save();

        sendJson($record);
    }

}