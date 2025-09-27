<?php

namespace App\Policies;

use App\Models\Matiere;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MatierePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any matières.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->nom_role, ['admin', 'coordinateur', 'enseignant']);
    }

    /**
     * Determine whether the user can view the matière.
     */
    public function view(User $user, Matiere $matiere): bool
    {
        return in_array($user->role->nom_role, ['admin', 'coordinateur', 'enseignant']);
    }

    /**
     * Determine whether the user can create matières.
     */
    public function create(User $user): bool
    {
        return $user->role->nom_role === 'admin';
    }

    /**
     * Determine whether the user can update the matière.
     */
    public function update(User $user, Matiere $matiere): bool
    {
        return $user->role->nom_role === 'admin';
    }

    /**
     * Determine whether the user can delete the matière.
     */
    public function delete(User $user, Matiere $matiere): bool
    {
        // Seul un admin peut supprimer une matière
        if ($user->role->nom_role !== 'admin') {
            return false;
        }

        // Vérifier si la matière est utilisée dans des séances
        return ! $matiere->seances()->exists();
    }
}
