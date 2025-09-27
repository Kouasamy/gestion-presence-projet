<?php

namespace App\Services;

use App\Models\Coordinateur;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\Parents;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Récupérer les utilisateurs par rôle
     */
    public function getUsersByRole(?string $roleName = null): Collection
    {
        if ($roleName) {
            return User::whereHas('role', function ($query) use ($roleName) {
                $query->where('nom_role', $roleName);
            })->get();
        } else {
            return User::all();
        }
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function createUser(array $data): User
    {
        // Gestion de la photo
        $photoPath = null;
        if (isset($data['photo_path']) && $data['photo_path'] instanceof UploadedFile) {
            $photoPath = $data['photo_path']->store('photos', 'public');
        }

        // Création de l'utilisateur
        $user = User::create([
            'nom' => $data['nom'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'photo_path' => $photoPath,
            'role_id' => $data['role_id'],
        ]);

        // Association du rôle à l'utilisateur
        $role = Role::find($data['role_id']);
        $roleName = trim(strtolower($role->nom_role));

        switch ($roleName) {
            case 'parent':
                Parents::create(['user_id' => $user->id]);
                break;

            case 'enseignant':
                Enseignant::create(['user_id' => $user->id]);
                break;

            case 'etudiant':
                Etudiant::create(['user_id' => $user->id]);
                break;

            case 'coordinateur':
                Coordinateur::create(['user_id' => $user->id]);
                break;
        }

        return $user;
    }

    /**
     * Mettre à jour un utilisateur existant
     */
    public function updateUser(User $user, array $data): User
    {
        // Gestion de la photo
        if (isset($data['photo_path']) && $data['photo_path'] instanceof UploadedFile) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo_path) {
                Storage::disk('public')->delete($user->photo_path);
            }
            $data['photo_path'] = $data['photo_path']->store('photos', 'public');
        } else {
            // Conserver l'ancienne photo
            unset($data['photo_path']);
        }

        // Gestion du mot de passe
        if (isset($data['password']) && ! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Mise à jour de l'utilisateur
        $user->update($data);

        // Gestion du changement de rôle
        if (isset($data['role_id']) && $user->role_id != $data['role_id']) {
            $oldRole = $user->role;
            $newRole = Role::find($data['role_id']);

            // Supprimer les anciennes associations de rôle
            $this->removeRoleAssociations($user, $oldRole->nom_role);

            // Créer les nouvelles associations de rôle
            $this->createRoleAssociations($user, $newRole->nom_role);
        }

        return $user;
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser(User $user): ?bool
    {
        // Supprimer la photo si elle existe
        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }

        // Supprimer les associations de rôle
        $this->removeRoleAssociations($user, $user->role->nom_role);

        // Supprimer l'utilisateur
        return $user->delete();
    }

    /**
     * Supprimer les associations de rôle d'un utilisateur
     */
    private function removeRoleAssociations(User $user, string $roleName): void
    {
        $roleName = trim(strtolower($roleName));

        switch ($roleName) {
            case 'parent':
                $user->parent()->delete();
                break;

            case 'enseignant':
                $user->enseignant()->delete();
                break;

            case 'etudiant':
                $user->etudiant()->delete();
                break;

            case 'coordinateur':
                $user->coordinateur()->delete();
                break;
        }
    }

    /**
     * Créer les associations de rôle pour un utilisateur
     */
    private function createRoleAssociations(User $user, string $roleName): void
    {
        $roleName = trim(strtolower($roleName));

        switch ($roleName) {
            case 'parent':
                Parents::create(['user_id' => $user->id]);
                break;

            case 'enseignant':
                Enseignant::create(['user_id' => $user->id]);
                break;

            case 'etudiant':
                Etudiant::create(['user_id' => $user->id]);
                break;

            case 'coordinateur':
                Coordinateur::create(['user_id' => $user->id]);
                break;
        }
    }

    /**
     * Assigner un parent à un étudiant
     */
    public function assignerParent(int $etudiantId, int $parentId): bool
    {
        $etudiant = Etudiant::findOrFail($etudiantId);
        $parent = Parents::findOrFail($parentId);

        // Vérifier si le parent est déjà assigné à cet étudiant
        if ($etudiant->parents()->where('parents.id', $parent->id)->exists()) {
            return false;
        }

        // Assigner le parent à l'étudiant
        $etudiant->parents()->attach($parent->id);

        return true;
    }

    /**
     * Désassigner un parent d'un étudiant
     */
    public function desassignerParent(int $etudiantId, int $parentId): bool
    {
        $etudiant = Etudiant::findOrFail($etudiantId);
        $parent = Parents::findOrFail($parentId);

        // Désassigner le parent de l'étudiant
        $etudiant->parents()->detach($parent->id);

        return true;
    }
}
