<?php

namespace App\Policies;

use App\Models\Presence;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PresencePolicy
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
    public function view(User $user, Presence $presence): bool
    {
        // Admin peut tout voir
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut voir les présences des séances qu'il gère
        if ($user->role->nom_role === 'coordinateur') {
            return $user->coordinateur && $presence->seance->coordinateur_id === $user->coordinateur->id;
        }

        // Enseignant peut voir les présences des séances qu'il enseigne
        if ($user->role->nom_role === 'enseignant') {
            return $user->enseignant && $presence->seance->enseignant_id === $user->enseignant->id;
        }

        // Étudiant peut voir ses propres présences
        if ($user->role->nom_role === 'etudiant') {
            return $user->etudiant && $presence->etudiant_id === $user->etudiant->id;
        }

        // Parent peut voir les présences de ses enfants
        if ($user->role->nom_role === 'parent') {
            return $user->parent && $user->parent->etudiants()
                ->where('etudiants.id', $presence->etudiant_id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role->nom_role, ['admin', 'coordinateur', 'enseignant']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Presence $presence): bool
    {
        // Admin peut tout modifier
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut modifier les présences des séances qu'il gère
        if ($user->role->nom_role === 'coordinateur') {
            return $user->coordinateur && $presence->seance->coordinateur_id === $user->coordinateur->id;
        }

        // Enseignant peut modifier les présences des séances qu'il enseigne
        if ($user->role->nom_role === 'enseignant') {
            return $user->enseignant && $presence->seance->enseignant_id === $user->enseignant->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Presence $presence): bool
    {
        // Admin peut tout supprimer
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut supprimer les présences des séances qu'il gère
        if ($user->role->nom_role === 'coordinateur') {
            return $user->coordinateur && $presence->seance->coordinateur_id === $user->coordinateur->id;
        }

        return false;
    }

    /**
     * Determine whether the user can justify the absence.
     */
    public function justify(User $user, Presence $presence): bool
    {
        // Vérifier que c'est bien une absence
        if ($presence->statut_presence_id !== 2) { // 2 = Absent
            return false;
        }

        // Vérifier que l'absence n'est pas déjà justifiée
        if ($presence->justificationAbsence) {
            return false;
        }

        // Admin peut tout justifier
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut justifier les absences des séances qu'il gère
        if ($user->role->nom_role === 'coordinateur') {
            return $user->coordinateur && $presence->seance->coordinateur_id === $user->coordinateur->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view justification details.
     */
    public function viewJustification(User $user, Presence $presence): bool
    {
        // Admin peut tout voir
        if ($user->role->nom_role === 'admin') {
            return true;
        }

        // Coordinateur peut voir les justifications des séances qu'il gère
        if ($user->role->nom_role === 'coordinateur') {
            return $user->coordinateur && $presence->seance->coordinateur_id === $user->coordinateur->id;
        }

        // Enseignant peut voir les justifications des séances qu'il enseigne
        if ($user->role->nom_role === 'enseignant') {
            return $user->enseignant && $presence->seance->enseignant_id === $user->enseignant->id;
        }

        // Étudiant peut voir ses propres justifications
        if ($user->role->nom_role === 'etudiant') {
            return $user->etudiant && $presence->etudiant_id === $user->etudiant->id;
        }

        // Parent peut voir les justifications de ses enfants
        if ($user->role->nom_role === 'parent') {
            return $user->parent && $user->parent->etudiants()
                ->where('etudiants.id', $presence->etudiant_id)
                ->exists();
        }

        return false;
    }
}
