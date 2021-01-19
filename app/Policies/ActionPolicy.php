<?php

namespace App\Policies;

use App\Action;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any actions.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the action.
     *
     * @param  \App\User  $user
     * @param  \App\Action  $action
     * @return mixed
     */
    public function view(User $user, Action $action)
    {
        return true;
    }

    /**
     * Determine whether the user can create actions.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the action.
     *
     * @param  \App\User  $user
     * @param  \App\Action  $action
     * @return mixed
     */
    public function update(User $user, Action $action)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the action.
     *
     * @param  \App\User  $user
     * @param  \App\Action  $action
     * @return mixed
     */
    public function delete(User $user, Action $action)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can restore the action.
     *
     * @param  \App\User  $user
     * @param  \App\Action  $action
     * @return mixed
     */
    public function restore(User $user, Action $action)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the action.
     *
     * @param  \App\User  $user
     * @param  \App\Action  $action
     * @return mixed
     */
    public function forceDelete(User $user, Action $action)
    {
        return $user->is_admin;
    }
}
