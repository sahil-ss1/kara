<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
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

    public function update(User $user, Organization $organization)
    {
        $userOrganization = $user->organization();
        return ( ($userOrganization && $userOrganization->id === $organization->id) || $user->isAdmin() )
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
