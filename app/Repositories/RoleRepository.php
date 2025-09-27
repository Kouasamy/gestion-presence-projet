<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    /**
     * Récupérer tous les rôles
     */
    public function getAll(): Collection
    {
        return Role::all();
    }

    /**
     * Récupérer un rôle par son ID
     */
    public function findById(int $id): ?Role
    {
        return Role::findOrFail($id);
    }

    /**
     * Récupérer un rôle par son nom
     */
    public function findByName(string $nomRole): ?Role
    {
        return Role::where('nom_role', $nomRole)->first();
    }

    /**
     * Créer un nouveau rôle
     */
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Mettre à jour un rôle
     */
    public function update(Role $role, array $data): Role
    {
        $role->update($data);

        return $role;
    }

    /**
     * Supprimer un rôle
     */
    public function delete(Role $role): ?bool
    {
        return $role->delete();
    }

    /**
     * Vérifier si un rôle est utilisé par des utilisateurs
     */
    public function isUsedByUsers(int $roleId): bool
    {
        return Role::findOrFail($roleId)->users()->exists();
    }

    /**
     * Récupérer le nombre d'utilisateurs par rôle
     */
    public function getUserCountByRole(): array
    {
        return Role::withCount('users')->get()->pluck('users_count', 'nom_role')->toArray();
    }
}
