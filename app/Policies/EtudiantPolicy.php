<?php

namespace App\Policies;

use App\Models\Etudiant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EtudiantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->nom_role, ['admin', 'coordinateur', 'enseignant', 'parent']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Etudiant $etudiant): bool
    {
        // Admin peut tout voir
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut voir tous les étudiants
        if ($user->role->nom_role === 'coordinateur') {
            return true;
        }

        // Enseignant peut voir les étudiants de ses classes
        if ($user->role->nom_role === 'enseignant') {
            return $user->enseignant && $user->enseignant->seances()
                ->whereHas('classe.etudiants', function ($query) use ($etudiant) {
                    $query->where('etudiants.id', $etudiant->id);
                })
                ->exists();
        }

        // Étudiant peut voir son propre profil
        if ($user->role->nom_role === 'etudiant') {
            return $user->etudiant && $user->etudiant->id === $etudiant->id;
        }

        // Parent peut voir ses enfants
        if ($user->role->nom_role === 'parent') {
            return $user->parent && $user->parent->etudiants()
                ->where('etudiants.id', $etudiant->id)
                ->exists();
        }

        return false;
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
    public function update(User $user, Etudiant $etudiant): bool
    {
        // Admin peut tout modifier
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Étudiant peut modifier son propre profil
        if ($user->role->nom_role === 'etudiant') {
            return $user->etudiant && $user->etudiant->id === $etudiant->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Etudiant $etudiant): bool
    {
        return $user->role->nom_role === 'admin';
    }

    /**
     * Determine whether the user can assign a class to the student.
     */
    public function assignerClasse(User $user, Etudiant $etudiant): bool
    {
        return in_array($user->role->nom_role, ['admin', 'coordinateur']);
    }

    /**
     * Determine whether the user can unassign a class from the student.
     */
    public function desinscrireClasse(User $user, Etudiant $etudiant): bool
    {
        return in_array($user->role->nom_role, ['admin', 'coordinateur']);
    }

    /**
     * Determine whether the user can view the student's statistics.
     */
    public function viewStatistiques(User $user, Etudiant $etudiant): bool
    {
        // Admin peut tout voir
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut voir les statistiques de tous les étudiants
        if ($user->role->nom_role === 'coordinateur') {
            return true;
        }

        // Enseignant peut voir les statistiques des étudiants de ses classes
        if ($user->role->nom_role === 'enseignant') {
            return $user->enseignant && $user->enseignant->seances()
                ->whereHas('classe.etudiants', function ($query) use ($etudiant) {
                    $query->where('etudiants.id', $etudiant->id);
                })
                ->exists();
        }

        // Étudiant peut voir ses propres statistiques
        if ($user->role->nom_role === 'etudiant') {
            return $user->etudiant && $user->etudiant->id === $etudiant->id;
        }

        // Parent peut voir les statistiques de ses enfants
        if ($user->role->nom_role === 'parent') {
            return $user->parent && $user->parent->etudiants()
                ->where('etudiants.id', $etudiant->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can view the student's absences.
     */
    public function viewAbsences(User $user, Etudiant $etudiant): bool
    {
        // Admin peut tout voir
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut voir les absences de tous les étudiants
        if ($user->role->nom_role === 'coordinateur') {
            return true;
        }

        // Enseignant peut voir les absences des étudiants de ses classes
        if ($user->role->nom_role === 'enseignant') {
            return $user->enseignant && $user->enseignant->seances()
                ->whereHas('classe.etudiants', function ($query) use ($etudiant) {
                    $query->where('etudiants.id', $etudiant->id);
                })
                ->exists();
        }

        // Étudiant peut voir ses propres absences
        if ($user->role->nom_role === 'etudiant') {
            return $user->etudiant && $user->etudiant->id === $etudiant->id;
        }

        // Parent peut voir les absences de ses enfants
        if ($user->role->nom_role === 'parent') {
            return $user->parent && $user->parent->etudiants()
                ->where('etudiants.id', $etudiant->id)
                ->exists();
        }

        return false;
    }
}
