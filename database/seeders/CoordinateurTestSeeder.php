<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\TypeCours;
use App\Models\StatutSeance;
use App\Models\User;
use App\Models\Coordinateur;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\Parents;
use App\Models\Seance;
use App\Models\Presence;
use App\Models\StatutPresence;
use App\Models\JustificationAbsence;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CoordinateurTestSeeder extends Seeder
{
    public function run()
    {
        // Années académiques
        $annee1 = AnneeAcademique::firstOrCreate(['annee' => '2023-2024']);
        $annee2 = AnneeAcademique::firstOrCreate(['annee' => '2024-2025']);

        // Classes
        $classeA = Classe::firstOrCreate(['nom_classe' => 'L1 Info']);
        $classeB = Classe::firstOrCreate(['nom_classe' => 'L2 Info']);

        // Matières
        $math = Matiere::firstOrCreate(['nom_matiere' => 'Mathématiques']);
        $prog = Matiere::firstOrCreate(['nom_matiere' => 'Programmation']);
        $reseau = Matiere::firstOrCreate(['nom_matiere' => 'Réseaux']);

        // Types de cours
        $cours = TypeCours::firstOrCreate(['nom_type_cours' => 'Cours']);
        $td = TypeCours::firstOrCreate(['nom_type_cours' => 'TD']);
        $tp = TypeCours::firstOrCreate(['nom_type_cours' => 'TP']);

        // Statuts de séance
        $statutPlanifiee = StatutSeance::firstOrCreate(['nom_seance' => 'Planifiée']);
        $statutAnnulee = StatutSeance::firstOrCreate(['nom_seance' => 'Annulée']);
        $statutReportee = StatutSeance::firstOrCreate(['nom_seance' => 'Reportée']);
        $statutTerminee = StatutSeance::firstOrCreate(['nom_seance' => 'Terminée']);

        // Rôles
        $roleCoord = Role::firstOrCreate(['nom_role' => 'coordinateur']);
        $roleEns = Role::firstOrCreate(['nom_role' => 'enseignant']);
        $roleEtu = Role::firstOrCreate(['nom_role' => 'etudiant']);
        $rolePar = Role::firstOrCreate(['nom_role' => 'parent']);

        // Coordinateur
        $userCoord = User::firstOrCreate(
            ['email' => 'coord@ifran.ci'],
            [
                'nom' => 'Coordinateur',
                'password' => Hash::make('password'), // ou un mot de passe par défaut
                'role_id' => $roleCoord->id,
                'photo_path' => null,
            ]
        );
        $coord = Coordinateur::firstOrCreate(['user_id' => $userCoord->id]);

        // Enseignants
        $enseignants = Enseignant::factory(3)->create()->each(function($ens) use ($roleEns) {
            $ens->user->update(['role_id' => $roleEns->id]);
        });

        // Étudiants
        $etudiants = Etudiant::factory(10)->create()->each(function($etu) use ($roleEtu) {
            $etu->user->update(['role_id' => $roleEtu->id]);
        });

        // Parents
        $parents = Parents::factory(3)->create()->each(function($par) use ($rolePar) {
            $par->user->update(['role_id' => $rolePar->id]);
        });

        // Liaisons parent-étudiant (1 parent pour 3-4 étudiants)
        foreach ($parents as $i => $parent) {
            $slice = $etudiants->slice($i*3, 3);
            $parent->etudiants()->sync($slice->pluck('id')->toArray());
        }

        // Liaisons étudiant-classe (répartis sur les deux classes)
        foreach ($etudiants as $i => $etudiant) {
            $classe = $i < 5 ? $classeA : $classeB;
            $etudiant->classes()->attach($classe->id, [
                'annee_academique_id' => $annee1->id,
                'date_debut' => '2023-09-01',
                'date_fin' => null
            ]);
        }

        // Liaisons classe-matière
        $classeA->matieres()->sync([$math->id, $prog->id]);
        $classeB->matieres()->sync([$prog->id, $reseau->id]);

        // Liaisons enseignant-matière
        $enseignants[0]->matieres()->sync([$math->id]);
        $enseignants[1]->matieres()->sync([$prog->id]);
        $enseignants[2]->matieres()->sync([$reseau->id]);

        // Séances (quelques séances pour chaque classe)
        $seances = [];
        foreach ([$classeA, $classeB] as $classe) {
            foreach ($classe->matieres as $matiere) {
                $enseignant = $enseignants->random();
                $seance = Seance::create([
                    'classe_id' => $classe->id,
                    'matiere_id' => $matiere->id,
                    'enseignant_id' => $enseignant->id,
                    'type_cours_id' => $cours->id,
                    'statut_seance_id' => $statutPlanifiee->id,
                    'date_seance' => now()->addDays(rand(-10, 10)),
                    'heure_debut' => '09:00',
                    'heure_fin' => '12:00',
                    'coordinateur_id' => $coord->id,
                ]);
                $seances[] = $seance;
            }
        }

        // Présences (pour chaque séance, chaque étudiant de la classe)
        $statutPresent = StatutPresence::where('nom_statut_presence', 'présent')->first();
        $statutAbsent = StatutPresence::where('nom_statut_presence', 'absent')->first();
        foreach ($seances as $seance) {
            $etudiantsClasse = $seance->classe->etudiants;
            foreach ($etudiantsClasse as $etudiant) {
                $statut = rand(0, 1) ? $statutPresent : $statutAbsent;
                $presence = Presence::create([
                    'seance_id' => $seance->id,
                    'etudiant_id' => $etudiant->id,
                    'statut_presence_id' => $statut->id,
                    'coordinateur_id' => $coord->id,
                    'justification' => null
                ]);
                // 1/4 des absences justifiées
                if ($statut->id == $statutAbsent->id && rand(0, 3) == 0) {
                    JustificationAbsence::create([
                        'presence_id' => $presence->id,
                        'motif' => 'Maladie',
                        'date_justification' => now(),
                    ]);
                }
            }
        }
    }
}
