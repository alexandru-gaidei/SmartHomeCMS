<?php

namespace App\Policies;

use App\User;
use App\VideoCamera;
use Illuminate\Auth\Access\HandlesAuthorization;

class VideoCameraPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any video cameras.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the video camera.
     *
     * @param  \App\User  $user
     * @param  \App\VideoCamera  $videoCamera
     * @return mixed
     */
    public function view(User $user, VideoCamera $cam)
    {
        return true;
    }

    /**
     * Determine whether the user can create video cameras.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the video camera.
     *
     * @param  \App\User  $user
     * @param  \App\VideoCamera  $videoCamera
     * @return mixed
     */
    public function update(User $user, VideoCamera $cam)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the video camera.
     *
     * @param  \App\User  $user
     * @param  \App\VideoCamera  $videoCamera
     * @return mixed
     */
    public function delete(User $user, VideoCamera $cam)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can restore the video camera.
     *
     * @param  \App\User  $user
     * @param  \App\VideoCamera  $videoCamera
     * @return mixed
     */
    public function restore(User $user, VideoCamera $cam)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the video camera.
     *
     * @param  \App\User  $user
     * @param  \App\VideoCamera  $videoCamera
     * @return mixed
     */
    public function forceDelete(User $user, VideoCamera $cam)
    {
        return $user->is_admin;
    }
}
