<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Auth\Access\HandlesAuthorization;

class AvailabilityPolicy
{
    use HandlesAuthorization;

    public function report(User $user): bool
    {
        return $user->is_active_atc || $user->is_visiting_atc || $user->isAdmin;
    }

}
