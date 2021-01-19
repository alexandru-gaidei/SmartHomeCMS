<?php

namespace App\Policies;

use App\Sensor;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SensorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any sensors.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the sensor.
     *
     * @param  \App\User  $user
     * @param  \App\Sensor  $sensor
     * @return mixed
     */
    public function view(User $user, Sensor $sensor)
    {
        return true;
    }

    /**
     * Determine whether the user can create sensors.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the sensor.
     *
     * @param  \App\User  $user
     * @param  \App\Sensor  $sensor
     * @return mixed
     */
    public function update(User $user, Sensor $sensor)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the sensor.
     *
     * @param  \App\User  $user
     * @param  \App\Sensor  $sensor
     * @return mixed
     */
    public function delete(User $user, Sensor $sensor)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can restore the sensor.
     *
     * @param  \App\User  $user
     * @param  \App\Sensor  $sensor
     * @return mixed
     */
    public function restore(User $user, Sensor $sensor)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the sensor.
     *
     * @param  \App\User  $user
     * @param  \App\Sensor  $sensor
     * @return mixed
     */
    public function forceDelete(User $user, Sensor $sensor)
    {
        return $user->is_admin;
    }
}
