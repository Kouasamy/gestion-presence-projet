<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Coordinateur;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\JustificationAbsence;
use App\Models\Matiere;
use App\Models\Parents;
use App\Models\Presence;
use App\Models\Role;
use App\Models\Seance;
use App\Models\StatutPresence;
use App\Models\StatutSeance;
use App\Models\TypeCours;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NewDataSeeder extends Seeder
{
    public function run()
    {
        // Années académiques
        $annee1 = AnneeAcademique::firstOrCreate(['annee' => '2023-2024']);
        $annee2 = AnneeAcademique::firstOrCreate(['annee' => '2024-2025']);

        // Classes (nouvelles classes demandées)
        $classeA = Classe::firstOrCreate(['nom_classe' => 'Prépa Dev']);
        $classeB = Classe::firstOrCreate(['nom_classe' => 'Prépa Créa']);
        $classeC = Classe::firstOrCreate(['nom_classe' => 'B2 Com']);

        // Matières (nouvelles matières demandées)
        // Langages de programmation
        $java = Matiere::firstOrCreate(['nom_matiere' => 'Java']);
        $python = Matiere::firstOrCreate(['nom_matiere' => 'Python']);
        $javascript = Matiere::firstOrCreate(['nom_matiere' => 'JavaScript']);
        $php = Matiere::firstOrCreate(['nom_matiere' => 'PHP']);

        // Marketing et design
        $uxDesign = Matiere::firstOrCreate(['nom_matiere' => 'UX Design']);
        $graphicDesign = Matiere::firstOrCreate(['nom_matiere' => 'Design Graphique']);
        $marketing = Matiere::firstOrCreate(['nom_matiere' => 'Marketing Digital']);
        $communication = Matiere::firstOrCreate(['nom_matiere' => 'Communication']);

        // Types de cours
        $cours = TypeCours::firstOrCreate(['nom_type_cours' => 'Cours']);
        $workshop = TypeCours::firstOrCreate(['nom_type_cours' => 'Workshop']);
        $elearning = TypeCours::firstOrCreate(['nom_type_cours' => 'E-learning']);

        // Statuts de séance
        $statutPlanifiee = StatutSeance::firstOrCreate(['nom_statut_seance' => 'programmée']);
        $statutAnnulee = StatutSeance::firstOrCreate(['nom_statut_seance' => 'annulée']);
        $statutReportee = StatutSeance::firstOrCreate(['nom_statut_seance' => 'reportée']);
        $statutTerminee = StatutSeance::firstOrCreate(['nom_statut_seance' => 'terminée']);

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
                'password' => Hash::make('password'),
                'role_id' => $roleCoord->id,
                'photo_path' => null,
            ]
        );
        $coord = Coordinateur::firstOrCreate(['user_id' => $userCoord->id]);

        // Enseignants
        $enseignants = Enseignant::factory(5)->create()->each(function ($ens) use ($roleEns) {
            $ens->user->update(['role_id' => $roleEns->id]);
        });

        // Étudiants
        $etudiants = Etudiant::factory(15)->create()->each(function ($etu) use ($roleEtu) {
            $etu->user->update(['role_id' => $roleEtu->id]);
        });

        // Parents
        $parents = Parents::factory(5)->create()->each(function ($par) use ($rolePar) {
            $par->user->update(['role_id' => $rolePar->id]);
        });

        // Liaisons parent-étudiant (1 parent pour 3 étudiants)
        foreach ($parents as $i => $parent) {
            $slice = $etudiants->slice($i * 3, 3);
            $parent->etudiants()->sync($slice->pluck('id')->toArray());
        }

        // Liaisons étudiant-classe (répartis sur les trois classes)
        foreach ($etudiants as $i => $etudiant) {
            if ($i < 5) {
                $classe = $classeA; // Prépa Dev
            } elseif ($i < 10) {
                $classe = $classeB; // Prépa Créa
            } else {
                $classe = $classeC; // B2 Com
            }

            $etudiant->classes()->attach($classe->id, [
                'annee_academique_id' => $annee1->id,
                'date_debut' => '2023-09-01',
                'date_fin' => null,
            ]);
        }

        // Liaisons classe-matière
        // Prépa Dev - langages de programmation
        $classeA->matieres()->sync([$java->id, $python->id, $javascript->id, $php->id]);

        // Prépa Créa - design
        $classeB->matieres()->sync([$uxDesign->id, $graphicDesign->id]);

        // B2 Com - marketing et communication
        $classeC->matieres()->sync([$marketing->id, $communication->id]);

        // Liaisons enseignant-matière
        $enseignants[0]->matieres()->sync([$java->id, $python->id]);
        $enseignants[1]->matieres()->sync([$javascript->id, $php->id]);
        $enseignants[2]->matieres()->sync([$uxDesign->id, $graphicDesign->id]);
        $enseignants[3]->matieres()->sync([$marketing->id]);
        $enseignants[4]->matieres()->sync([$communication->id]);

        // Séances (quelques séances pour chaque classe)
        $seances = [];
        foreach ([$classeA, $classeB, $classeC] as $classe) {
            foreach ($classe->matieres as $matiere) {
                $enseignant = $enseignants->random();
                $typeCours = rand(0, 2) == 0 ? $cours : (rand(0, 1) == 0 ? $workshop : $elearning);

                $seance = Seance::create([
                    'classe_id' => $classe->id,
                    'matiere_id' => $matiere->id,
                    'enseignant_id' => $enseignant->id,
                    'type_cours_id' => $typeCours->id,
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
        $statutRetard = StatutPresence::where('nom_statut_presence', 'retard')->first();

        foreach ($seances as $seance) {
            $etudiantsClasse = $seance->classe->etudiants;
            foreach ($etudiantsClasse as $etudiant) {
                $rand = rand(0, 10);
                $statut = $rand < 7 ? $statutPresent : ($rand < 9 ? $statutRetard : $statutAbsent);

                $presence = Presence::create([
                    'seance_id' => $seance->id,
                    'etudiant_id' => $etudiant->id,
                    'statut_presence_id' => $statut->id,
                    'coordinateur_id' => $coord->id,
                ]);

                // 1/3 des absences justifiées
                if ($statut->id == $statutAbsent->id && rand(0, 2) == 0) {
                    $motifs = ['Maladie', 'Rendez-vous médical', 'Problème familial', 'Transport'];
                    JustificationAbsence::create([
                        'presence_id' => $presence->id,
                        'motif' => $motifs[array_rand($motifs)],
                        'date_justification' => now(),
                        'document_path' => null,
                    ]);
                }
            }
        }
    }
}
