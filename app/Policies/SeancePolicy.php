<?php

namespace App\Policies;

use App\Models\Seance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->nom_role, ['admin', 'coordinateur', 'enseignant', 'etudiant', 'parent']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Seance $seance): bool
    {
        // Admin peut tout voir
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut voir ses séances
        if ($user->role->nom_role === 'coordinateur') {
            return $user->coordinateur && $seance->coordinateur_id === $user->coordinateur->id;
        }

        // Enseignant peut voir ses séances
        if ($user->role->nom_role === 'enseignant') {
            return $user->enseignant && $seance->enseignant_id === $user->enseignant->id;
        }

        // Étudiant peut voir les séances de sa classe
        if ($user->role->nom_role === 'etudiant') {
            return $user->etudiant && $user->etudiant->classes()->where('classes.id', $seance->classe_id)->exists();
        }

        // Parent peut voir les séances des classes de ses enfants
        if ($user->role->nom_role === 'parent') {
            return $user->parent && $user->parent->etudiants()
                ->whereHas('classes', function ($query) use ($seance) {
                    $query->where('classes.id', $seance->classe_id);
                })
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role->nom_role, ['admin', 'coordinateur']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Seance $seance): bool
    {
        // Admin peut tout modifier
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut modifier ses séances
        if ($user->role->nom_role === 'coordinateur') {
            return $user->coordinateur && $seance->coordinateur_id === $user->coordinateur->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Seance $seance): bool
    {
        // Admin peut tout supprimer
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut supprimer ses séances
        if ($user->role->nom_role === 'coordinateur') {
            return $user->coordinateur && $seance->coordinateur_id === $user->coordinateur->id;
        }

        return false;
    }

    /**
     * Determine whether the user can manage presences for the model.
     */
    public function managePresences(User $user, Seance $seance): bool
    {
        // Admin peut tout gérer
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut gérer les présences de ses séances
        if ($user->role->nom_role === 'coordinateur') {
            return $user->coordinateur && $seance->coordinateur_id === $user->coordinateur->id;
        }

        // Enseignant peut gérer les présences de ses séances
        if ($user->role->nom_role === 'enseignant') {
            return $user->enseignant && $seance->enseignant_id === $user->enseignant->id;
        }

        return false;
    }

    /**
     * Determine whether the user can report the model.
     */
    public function report(User $user, Seance $seance): bool
    {
        // Admin peut tout reporter
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut reporter ses séances
        if ($user->role->nom_role === 'coordinateur') {
            return $user->coordinateur && $seance->coordinateur_id === $user->coordinateur->id;
        }

        return false;
    }

    /**
     * Determine whether the user can cancel the model.
     */
    public function cancel(User $user, Seance $seance): bool
    {
        // Admin peut tout annuler
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut annuler ses séances
        if ($user->role->nom_role === 'coordinateur') {
            return $user->coordinateur && $seance->coordinateur_id === $user->coordinateur->id;
        }

        return false;
    }
}
