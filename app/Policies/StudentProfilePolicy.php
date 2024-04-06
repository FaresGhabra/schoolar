<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\SchoolAccounts\Student;
use App\Models\SchoolAccounts\User;
use Illuminate\Auth\Access\Response;

class StudentProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->role == RoleEnum::OWNER->value ? Response::allow()
            : sendMessageJson('Unauthorized', 401);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Student $student)
    {
        $a = [RoleEnum::OWNER->value, RoleEnum::ADMIN->value];
        
        if (in_array($user->role_id, $a))
            return Response::allow();

        if ($user->id !== $student->user_id)
            sendMessageJson('Unauthorized', 401);
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function create(User $user)
    {
        $a = [RoleEnum::OWNER->value, RoleEnum::ADMIN->value];
        if (in_array($user->role_id, $a))
            return Response::allow();
        else
            sendMessageJson('Unauthorized', 401);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user)
    {
        $a = [RoleEnum::OWNER->value, RoleEnum::ADMIN->value];
        if (in_array($user->role_id, $a))
            return Response::allow();
        else
            sendMessageJson('Unauthorized', 401);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deactive(User $user)
    {
        $a = [RoleEnum::OWNER->value, RoleEnum::ADMIN->value];
        if (in_array($user->role_id, $a))
            return Response::allow();
        else
            sendMessageJson('Unauthorized', 401);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user)
    {
        $a = [RoleEnum::OWNER->value, RoleEnum::ADMIN->value];
        if (in_array($user->role_id, $a))
            return Response::allow();
        else
            sendMessageJson('Unauthorized', 401);

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user)
    {
        $a = [RoleEnum::OWNER->value, RoleEnum::ADMIN->value];
        if (in_array($user->role_id, $a))
            return Response::allow();
        else
            sendMessageJson('Unauthorized', 401);
    }
}