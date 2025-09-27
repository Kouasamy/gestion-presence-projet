<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\StatutPresence;
use App\Models\StatutSeance;
use App\Models\TypeCours;
use Illuminate\Database\Seeder;

class StatutsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les rôles de base
        $roles = [
            ['nom_role' => 'admin'],
            ['nom_role' => 'coordinateur'],
            ['nom_role' => 'enseignant'],
            ['nom_role' => 'etudiant'],
            ['nom_role' => 'parent'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }

        // Créer les statuts de présence
        $statutsPresence = [
            ['nom_statut_presence' => 'présent'],
            ['nom_statut_presence' => 'absent'],
            ['nom_statut_presence' => 'retard'],
        ];

        foreach ($statutsPresence as $statut) {
            StatutPresence::firstOrCreate($statut);
        }

        // Créer les statuts de séance
        $statutsSeance = [
            ['nom_statut_seance' => 'programmée'],
            ['nom_statut_seance' => 'en_cours'],
            ['nom_statut_seance' => 'terminée'],
            ['nom_statut_seance' => 'annulée'],
            ['nom_statut_seance' => 'reportée'],
        ];

        foreach ($statutsSeance as $statut) {
            StatutSeance::firstOrCreate($statut);
        }

        // Créer les types de cours
        $typesCours = [
            ['nom_type_cours' => 'présentiel'],
            ['nom_type_cours' => 'e-learning'],
            ['nom_type_cours' => 'workshop'],
            ['nom_type_cours' => 'td'],
            ['nom_type_cours' => 'tp'],
        ];

        foreach ($typesCours as $type) {
            TypeCours::firstOrCreate($type);
        }
    }
}
