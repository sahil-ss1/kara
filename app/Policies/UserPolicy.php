<?php

namespace App\Policies;

use App\Models\User;
use Auth;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, User $model)
    {
        return ( ($model->id === Auth::user()->id) || $user->isAdmin() )
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
