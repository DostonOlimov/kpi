<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CommissionPolicy
{
    use HandlesAuthorization;

    public function scoresList(User $user): Response
    {
        return $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_KADRLAR, User::ROLE_IJRO])
            ? Response::allow()
            : Response::deny('Sizda bu sahifaga kirish huquqi yo\'q!');
    }

    public function activity(User $user): Response
    {
        return $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_MANAGER,User::ROLE_RAHBAR])
            ? Response::allow()
            : Response::deny('Sizda bu sahifaga kirish huquqi yo\'q!');
    }
}
