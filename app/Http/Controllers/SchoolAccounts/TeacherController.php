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

class TeacherController extends Controller
{
    public function show($id)
    {
        $teacher  = Teacher::find($id);
        sendJson($teacher);
    }

    public function getTeacherSections($id) {
        $teacher  = Teacher::find($id);
        sendJson($teacher->sections);
    }
    public function getTeacherSubjects($id) {
        $teacher  = Teacher::find($id);
        sendJson($teacher->subjects);
    }

}