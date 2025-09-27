<?php

namespace App\Services;

use App\Models\Role;

class RoleService
{
    /**
     * Créer un nouveau rôle
     */
    public function createRole(array $data): Role
    {
        return Role::create([
            'nom_role' => $data['nom_role'],
        ]);
    }

    /**
     * Mettre à jour un rôle existant
     */
    public function updateRole(Role $role, array $data): Role
    {
        $role->update([
            'nom_role' => $data['nom_role'],
        ]);

        return $role;
    }

    /**
     * Supprimer un rôle
     */
    public function deleteRole(Role $role): ?bool
    {
        return $role->delete();
    }

    /**
     * Récupérer tous les rôles
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles()
    {
        return Role::all();
    }

    /**
     * Récupérer un rôle par son ID
     */
    public function getRoleById(int $id): Role
    {
        return Role::findOrFail($id);
    }
}
