<?php

namespace Database\Seeders;

use App\Models\Coordinateur;
use App\Models\Role;
use App\Models\StatutPresence;
use App\Models\StatutSeance;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // D'abord créer les données de référence
        $this->call([
            StatutsSeeder::class,
            TypeCoursSeeder::class,
        ]);

        // Ensuite créer l'admin
        $adminRole = Role::where('nom_role', 'admin')->first();
        User::firstOrCreate([
            'email' => 'admin@ifran.ci',
        ], [
            'nom' => 'Admin',
            'password' => Hash::make('admin12345'),
            'role_id' => $adminRole->id,
            'photo_path' => null,
        ]);

        // Enfin, appeler les seeders de test
        $this->call([
            NewDataSeeder::class,
        ]);
    }
}
