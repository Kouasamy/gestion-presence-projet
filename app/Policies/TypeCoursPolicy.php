<?php

namespace App\Policies;

use App\Models\TypeCours;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TypeCoursPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any types de cours.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->nom_role, ['admin', 'coordinateur', 'enseignant']);
    }

    /**
     * Determine whether the user can view the type de cours.
     */
    public function view(User $user, TypeCours $typeCours): bool
    {
        return in_array($user->role->nom_role, ['admin', 'coordinateur', 'enseignant']);
    }

    /**
     * Determine whether the user can create types de cours.
     */
    public function create(User $user): bool
    {
        return $user->role->nom_role === 'admin';
    }

    /**
     * Determine whether the user can update the type de cours.
     */
    public function update(User $user, TypeCours $typeCours): bool
    {
        return $user->role->nom_role === 'admin';
    }

    /**
     * Determine whether the user can delete the type de cours.
     */
    public function delete(User $user, TypeCours $typeCours): bool
    {
        // Seul un admin peut supprimer un type de cours
        if ($user->role->nom_role !== 'admin') {
            return false;
        }

        // Vérifier si le type de cours est utilisé dans des séances
        return ! $typeCours->seances()->exists();
    }
}
