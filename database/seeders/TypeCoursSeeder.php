<?php

namespace Database\Seeders;

use App\Models\TypeCours;
use Illuminate\Database\Seeder;

class TypeCoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typesCours = [
            ['nom_type_cours' => 'Cours'],
            ['nom_type_cours' => 'Workshop'],
            ['nom_type_cours' => 'E-learning'],
        ];

        foreach ($typesCours as $typeCours) {
            TypeCours::updateOrCreate(
                ['nom_type_cours' => $typeCours['nom_type_cours']],
                $typeCours
            );
        }
    }
}
