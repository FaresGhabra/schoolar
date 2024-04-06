<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\SchoolAccounts\OnlineStudent;
use App\Models\SchoolAccounts\User;
use Illuminate\Auth\Access\Response;

class OnlineStudentProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->role_id == RoleEnum::OWNER->value ? Response::allow()
            : sendMessageJson('Unauthorized', 401);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OnlineStudent $onlineStudent)
    {
        if ($user->role_id === RoleEnum::OWNER->value)
            return Response::allow();

        if ($user->id !== $onlineStudent->user_id)
            sendMessageJson('Unauthorized.', 401);
        return true;
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OnlineStudent $onlineStudent): Response
    {
        if ($user->role_id === RoleEnum::OWNER->value)
            return Response::allow();

        return $user->id === $onlineStudent->user_id ? Response::allow()
            : sendMessageJson('Unauthorized', 401);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OnlineStudent $onlineStudent): Response
    {
        return $user->role_id == RoleEnum::OWNER->value ? Response::allow()
            : sendMessageJson('Unauthorized', 401);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OnlineStudent $onlineStudent): Response
    {
        return $user->role_id == RoleEnum::OWNER->value ? Response::allow()
            : sendMessageJson('Unauthorized', 401);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OnlineStudent $onlineStudent): Response
    {
        return $user->role_id == RoleEnum::OWNER->value ? Response::allow()
            : sendMessageJson('Unauthorized', 401);
    }
}