<?php

namespace Database\Seeders;

use App\Models\Coordinateur;
use App\Models\Enseignant;
use App\Models\Parents;
use App\Models\Role;
use App\Models\User;
use App\Models\StatutPresence;
use App\Models\StatutSeance;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['nom_role' => 'admin']);
        // Créer 1 admin sans attach
        User::firstOrCreate([
            'email' => 'admin@ifran.ci',
        ], [
            'nom' => 'Admin',
            'password' => Hash::make('admin12345'),
            'role_id' => $adminRole['id'],
            'photo_path' => null,
        ]);

        // Statuts de présence de base
        foreach(['présent', 'absent', 'retard'] as $statut) {
            StatutPresence::firstOrCreate(['nom_statut_presence' => $statut]);
        }

        // Statuts de séance de base
        foreach(['planifiée', 'en cours', 'terminée', 'annulée'] as $statut) {
            StatutSeance::firstOrCreate(['nom_seance' => $statut]);
        }

        // Appel des seeders pour l'espace coordinateur
        $this->call([
            CoordinateurTestSeeder::class,
        ]);
    }
}
