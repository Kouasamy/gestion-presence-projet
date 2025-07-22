<?php

namespace Database\Seeders;

use App\Models\Coordinateur;
use App\Models\Enseignant;
use App\Models\Parents;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::create(['nom_role' => 'admin']);


        // Créer 1 admin sans attach
        User::create([
            'nom' => 'Admin',
            'email' => 'admin@ifran.ci',
            'password' => Hash::make('admin12345'),
            'role_id' => $adminRole['id'],
            'photo_path' => null,
        ]);

    }
}
