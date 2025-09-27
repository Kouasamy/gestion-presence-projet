<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role->nom_role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->role->nom_role === 'admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->nom_role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->role->nom_role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        // Seul un admin peut supprimer un rôle
        if ($user->role->nom_role !== 'admin') {
            return false;
        }

        // Empêcher la suppression du rôle admin
        if ($role->nom_role === 'admin') {
            return false;
        }

        // Vérifier si le rôle est utilisé par des utilisateurs
        return ! $role->users()->exists();
    }
}
