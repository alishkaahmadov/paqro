<?php

namespace App\Policies;

use App\Models\User;

class DashboardPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user)
    {
        return true; // All users can view
    }
    
    public function create(User $user)
    {
        return $user->is_admin; // Only admins can create
    }

    public function delete(User $user)
    {
        return $user->is_admin; // Only admins can delete
    }

}
