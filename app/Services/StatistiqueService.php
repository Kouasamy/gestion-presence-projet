<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Etudiant;
use App\Models\Presence;
use App\Models\Seance;
use App\Models\Semestre;
use App\Models\TypeCours;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatistiqueService
{
    protected $semestreService;

    public function __construct(SemestreService $semestreService)
    {
        $this->semestreService = $semestreService;
    }

    /**
     * Déterminer le semestre pour une séance en fonction de sa date
     */
    protected function getSemestreForDate($date): ?Semestre
    {
        return Semestre::where('date_debut_semestre', '<=', $date)
            ->where('date_fin_semestre', '>=', $date)
            ->first();
    }

    /**
     * Récupérer les statistiques pour le tableau de bord
     */
    public function getStatistiques(): array
    {
        try {
            if (!Auth::check() || !Auth::user()->coordinateur) {
                return $this->getStatistiquesVides();
            }

            return $this->getStatistiquesCoordinateur();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
            return $this->getStatistiquesVides();
        }
    }

    /**
     * Récupérer des statistiques vides en cas d'erreur
     */
    protected function getStatistiquesVides(): array
    {
        return [
            'tauxPresenceEtudiants' => collect([]),
            'etudiantsDroppes' => collect([]),
            'seancesAujourdhui' => 0,
            'presencesASaisir' => 0,
            'absencesNonJustifiees' => 0,
        ];
    }

    /**
     * Récupérer les statistiques pour le tableau de bord du coordinateur
     */
    public function getStatistiquesCoordinateur(): array
    {
        try {
            if (!Auth::check() || !Auth::user()->coordinateur) {
                return $this->getStatistiquesVides();
            }

            $coordinateurId = Auth::user()->coordinateur->id;

            // Nombre de séances prévues aujourd'hui
            $seancesAujourdhui = Seance::where('coordinateur_id', $coordinateurId)
                ->whereDate('date_seance', Carbon::today())
                ->count();

            // Nombre de séances sans présences saisies
            $presencesASaisir = Seance::where('coordinateur_id', $coordinateurId)
                ->whereDoesntHave('presences')
                ->count();

            // Nombre d'absences non justifiées
            $absencesNonJustifiees = Presence::whereHas('seance', function ($q) use ($coordinateurId) {
                $q->where('coordinateur_id', $coordinateurId);
            })
                ->where('statut_presence_id', 2) // 2 = Absent
                ->whereDoesntHave('justificationAbsence')
                ->count();

            // Taux de présence par étudiant
            $tauxPresenceEtudiants = Etudiant::with('user')
                ->whereHas('presences')
                ->get()
                ->map(function ($etudiant) {
                    $total = $etudiant->presences()->count();
                    $absents = $etudiant->presences()
                        ->where('statut_presence_id', 2)
                        ->count();

                    $taux = $total > 0 ? round(100 - ($absents / $total * 100), 1) : 0;
                    $couleur = $this->getCouleurTaux($taux);

                    return [
                        'id' => $etudiant->id,
                        'nom' => $etudiant->user->nom ?? 'Étudiant ' . $etudiant->id,
                        'photo' => $etudiant->user->photo_url ?? 'https://randomuser.me/api/portraits/men/'.($etudiant->id % 100).'.jpg',
                        'taux' => $taux,
                        'couleur' => $couleur,
                        'dropped' => $taux < 30,
                    ];
                });

            // Étudiants avec taux < 30%
            $etudiantsDroppes = $tauxPresenceEtudiants->filter(fn ($e) => $e['dropped'])->values();

            return [
                'tauxPresenceEtudiants' => $tauxPresenceEtudiants,
                'etudiantsDroppes' => $etudiantsDroppes,
                'seancesAujourdhui' => $seancesAujourdhui,
                'presencesASaisir' => $presencesASaisir,
                'absencesNonJustifiees' => $absencesNonJustifiees,
            ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques du coordinateur: ' . $e->getMessage());
            return $this->getStatistiquesVides();
        }
    }

    /**
     * Récupérer les statistiques détaillées
     */
    public function getStatistiquesDetaillees(array $filters = []): array
    {
        try {
            // Récupérer les objets depuis les filtres ou les derniers par défaut
            $anneeObj = isset($filters['annee'])
                ? AnneeAcademique::find($filters['annee'])
                : AnneeAcademique::latest()->first();

            $semestreObj = isset($filters['semestre'])
                ? Semestre::find($filters['semestre'])
                : ($anneeObj ? $this->semestreService->getSemestresForAnnee($anneeObj->id)->first() : null);

            $classeId = $filters['classe'] ?? null;

            // Initialiser les variables
            $tauxPresenceEtudiants = [];
            $tauxPresenceClasses = [];
            $volumeCoursParType = [];
            $volumeCumuleCours = [];

            // Calculer les données seulement si les filtres sont valides
            if ($anneeObj) {
                $tauxPresenceEtudiants = $this->getTauxPresenceEtudiants($anneeObj, $semestreObj, $classeId);
                $tauxPresenceClasses = $this->getTauxPresenceClasses($anneeObj, $semestreObj, $classeId);
                $volumeCoursParType = $this->getVolumeCoursParType($anneeObj, $semestreObj, $classeId);
                $volumeCumuleCours = $this->getVolumeCumuleCours($anneeObj, $semestreObj, $classeId);
            } else {
                // Données par défaut si aucune année ou semestre n'est trouvé
                $tauxPresenceEtudiants = [
                    ['nom' => 'Aucune donnée', 'taux' => 0, 'couleur' => '#6b7280']
                ];
                $tauxPresenceClasses = [
                    ['classe' => 'Aucune donnée', 'taux' => 0]
                ];
                $volumeCoursParType = $this->getVolumeCoursParTypeVide();
                $volumeCumuleCours = $this->getVolumeCumuleCoursVide();
            }

            return [
                'tauxPresenceEtudiants' => $tauxPresenceEtudiants,
                'tauxPresenceClasses' => $tauxPresenceClasses,
                'volumeCoursParType' => $volumeCoursParType,
                'volumeCumuleCours' => $volumeCumuleCours,
            ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques détaillées: ' . $e->getMessage());

            // Retourner des données par défaut en cas d'erreur
            return [
                'tauxPresenceEtudiants' => [
                    ['nom' => 'Erreur de données', 'taux' => 0, 'couleur' => '#6b7280']
                ],
                'tauxPresenceClasses' => [
                    ['classe' => 'Erreur de données', 'taux' => 0]
                ],
                'volumeCoursParType' => $this->getVolumeCoursParTypeVide('Erreur de données'),
                'volumeCumuleCours' => $this->getVolumeCumuleCoursVide('Erreur de données'),
            ];
        }
    }

    /**
     * Récupérer le taux de présence des étudiants
     */
    protected function getTauxPresenceEtudiants(?AnneeAcademique $anneeObj = null, ?Semestre $semestreObj = null, ?int $classeId = null): array
    {
        try {
            if (!$anneeObj) {
                return [
                    ['nom' => 'Aucune donnée', 'taux' => 0, 'couleur' => '#6b7280']
                ];
            }

            $startDate = $semestreObj ? $semestreObj->date_debut_semestre : null;
            $endDate = $semestreObj ? $semestreObj->date_fin_semestre : null;

            $query = Etudiant::with('user')
                ->whereHas('presences');

            // Filtrer par année académique
            $anneeId = $anneeObj->id;
            $query->whereHas('classes', function ($q) use ($anneeId) {
                $q->where('etudiant_classe.annee_academique_id', $anneeId);
            });

            // Filtrer par classe si spécifié
            if ($classeId) {
                $query->whereHas('classes', function ($q) use ($classeId) {
                    $q->where('classes.id', $classeId);
                });
            }

            $etudiants = $query->get();

            if ($etudiants->isEmpty()) {
                return [
                    ['nom' => 'Aucun étudiant trouvé', 'taux' => 0, 'couleur' => '#6b7280']
                ];
            }

            return $etudiants
                ->map(function ($etudiant) use ($startDate, $endDate, $classeId) {
                    $presencesQuery = Presence::where('etudiant_id', $etudiant->id);

                    // Filtrer par plage de dates du semestre
                    if ($startDate && $endDate) {
                        $presencesQuery->whereHas('seance', function ($q) use ($startDate, $endDate) {
                            $q->whereBetween('date_seance', [$startDate, $endDate]);
                        });
                    }

                    // Filtrer par classe
                    if ($classeId) {
                        $presencesQuery->whereHas('seance', function ($q) use ($classeId) {
                            $q->where('classe_id', $classeId);
                        });
                    }

                    $totalPresences = $presencesQuery->count();
                    $absences = (clone $presencesQuery)->where('statut_presence_id', 2)->count();

                    $taux = $totalPresences > 0
                        ? round(100 - ($absences / $totalPresences * 100), 1)
                        : 0;

                    return [
                        'nom' => $etudiant->user->nom ?? 'Étudiant ' . $etudiant->id,
                        'taux' => $taux,
                        'couleur' => $this->getCouleurTaux($taux),
                    ];
                })
                ->sortByDesc('taux')
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du taux de présence des étudiants: ' . $e->getMessage());
            return [
                ['nom' => 'Erreur de données', 'taux' => 0, 'couleur' => '#6b7280']
            ];
        }
    }

    /**
     * Récupérer le taux de présence par classe
     */
    protected function getTauxPresenceClasses(?AnneeAcademique $anneeObj = null, ?Semestre $semestreObj = null, ?int $classeIdFilter = null): array
    {
        try {
            if (!$anneeObj) {
                return [
                    ['classe' => 'Aucune donnée', 'taux' => 0]
                ];
            }

            $startDate = $semestreObj ? $semestreObj->date_debut_semestre : null;
            $endDate = $semestreObj ? $semestreObj->date_fin_semestre : null;
            $anneeId = $anneeObj->id;

            // Récupérer toutes les classes qui ont des étudiants liés à cette année académique
            $classeIds = DB::table('etudiant_classe')
                ->where('annee_academique_id', $anneeId)
                ->pluck('classe_id')
                ->unique();

            // Si un filtre de classe est spécifié, ne garder que cette classe
            if ($classeIdFilter) {
                $classeIds = $classeIds->filter(function ($id) use ($classeIdFilter) {
                    return $id == $classeIdFilter;
                });
            }

            $classes = Classe::whereIn('id', $classeIds)->get();

            if ($classes->isEmpty()) {
                return [
                    ['classe' => 'Aucune classe trouvée', 'taux' => 0]
                ];
            }

            return $classes->map(function ($classe) use ($startDate, $endDate) {
                $query = Presence::whereHas('seance', function ($q) use ($classe, $startDate, $endDate) {
                    $q->where('classe_id', $classe->id);
                    if ($startDate && $endDate) {
                        $q->whereBetween('date_seance', [$startDate, $endDate]);
                    }
                });

                $totalPresences = $query->count();
                $absences = (clone $query)->where('statut_presence_id', 2)->count();

                $taux = $totalPresences > 0
                    ? round(100 - ($absences / $totalPresences * 100), 1)
                    : 0;

                return [
                    'classe' => $classe->nom_classe,
                    'taux' => $taux,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du taux de présence par classe: ' . $e->getMessage());
            return [
                ['classe' => 'Erreur de données', 'taux' => 0]
            ];
        }
    }

    /**
     * Récupérer le volume de cours par type sur les 6 derniers mois
     */
    protected function getVolumeCoursParType(?AnneeAcademique $anneeObj = null, ?Semestre $semestreObj = null, ?int $classeId = null): array
    {
        try {
            if (!$anneeObj) {
                return $this->getVolumeCoursParTypeVide();
            }

            $typesCours = TypeCours::all();

            if ($typesCours->isEmpty()) {
                return $this->getVolumeCoursParTypeVide('Aucun type de cours trouvé');
            }

            $mois = collect()->range(0, 5)
                ->map(fn ($i) => now()->subMonths($i)->format('Y-m'))
                ->reverse()
                ->values();

            $dateDebutSemestre = $semestreObj ? $semestreObj->date_debut_semestre : null;
            $dateFinSemestre = $semestreObj ? $semestreObj->date_fin_semestre : null;
            $anneeId = $anneeObj->id;

            $datasets = $typesCours->map(function ($type) use ($mois, $anneeId, $dateDebutSemestre, $dateFinSemestre, $classeId) {
                $volumes = $mois->map(function ($moisAnnee) use ($type, $anneeId, $dateDebutSemestre, $dateFinSemestre, $classeId) {
                    $query = Seance::where('type_cours_id', $type->id)
                        ->whereRaw("DATE_FORMAT(date_seance, '%Y-%m') = ?", [$moisAnnee]);

                    // Filtrer par année académique
                    $query->whereHas('classe.etudiants', function ($q) use ($anneeId) {
                        $q->where('etudiant_classe.annee_academique_id', $anneeId);
                    });

                    // Filtrer par plage de dates du semestre
                    if ($dateDebutSemestre && $dateFinSemestre) {
                        $query->whereBetween('date_seance', [$dateDebutSemestre, $dateFinSemestre]);
                    }

                    // Filtrer par classe
                    if ($classeId) {
                        $query->where('classe_id', $classeId);
                    }

                    return $query->count();
                });

                return [
                    'label' => $type->nom_type_cours,
                    'data' => $volumes->toArray(),
                    'backgroundColor' => $this->getColorForTypeCours($type->nom_type_cours),
                ];
            });

            return [
                'labels' => $mois->map(fn ($m) => date('M Y', strtotime($m.'-01')))->toArray(),
                'datasets' => $datasets->toArray(),
            ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du volume de cours par type: ' . $e->getMessage());
            return $this->getVolumeCoursParTypeVide('Erreur de données');
        }
    }

    /**
     * Récupérer des données vides pour le volume de cours par type
     */
    protected function getVolumeCoursParTypeVide(string $label = 'Aucune donnée'): array
    {
        return [
            'labels' => [$label],
            'datasets' => [
                [
                    'label' => $label,
                    'data' => [0],
                    'backgroundColor' => '#6b7280'
                ]
            ]
        ];
    }

    /**
     * Récupérer le volume cumulé de cours par type sur les 4 derniers trimestres
     */
    protected function getVolumeCumuleCours(?AnneeAcademique $anneeObj = null, ?Semestre $semestreObj = null, ?int $classeId = null): array
    {
        try {
            if (!$anneeObj) {
                return $this->getVolumeCumuleCoursVide();
            }

            $now = now();
            $trimestres = collect()->range(0, 3)
                ->map(function ($i) use ($now) {
                    $date = $now->copy()->subMonths($i * 3);
                    $trimestre = ceil($date->month / 3);
                    $year = $date->year;
                    return "T{$trimestre} {$year}";
                })
                ->reverse()
                ->values();

            $typesCours = TypeCours::all();

            if ($typesCours->isEmpty()) {
                return $this->getVolumeCumuleCoursVide('Aucun type de cours trouvé');
            }

            $dateDebutSemestre = $semestreObj ? $semestreObj->date_debut_semestre : null;
            $dateFinSemestre = $semestreObj ? $semestreObj->date_fin_semestre : null;
            $anneeId = $anneeObj->id;

            $datasets = $typesCours->map(function ($type) use ($now, $anneeId, $dateDebutSemestre, $dateFinSemestre, $classeId) {
                $data = collect()->range(0, 3)->map(function ($i) use ($now, $type, $anneeId, $dateDebutSemestre, $dateFinSemestre, $classeId) {
                    $date = $now->copy()->subMonths($i * 3);
                    $startMonth = ceil($date->month / 3) * 3 - 2;
                    $endMonth = $startMonth + 2;

                    $query = Seance::where('type_cours_id', $type->id)
                        ->whereYear('date_seance', $date->year)
                        ->whereMonth('date_seance', '>=', $startMonth)
                        ->whereMonth('date_seance', '<=', $endMonth);

                    // Filtrer par année académique
                    $query->whereHas('classe.etudiants', function ($q) use ($anneeId) {
                        $q->where('etudiant_classe.annee_academique_id', $anneeId);
                    });

                    // Filtrer par plage de dates du semestre
                    if ($dateDebutSemestre && $dateFinSemestre) {
                        $query->whereBetween('date_seance', [$dateDebutSemestre, $dateFinSemestre]);
                    }

                    // Filtrer par classe
                    if ($classeId) {
                        $query->where('classe_id', $classeId);
                    }

                    return $query->count();
                })->reverse()->values();

                $color = $this->getColorForTypeCours($type->nom_type_cours);

                return [
                    'label' => $type->nom_type_cours,
                    'data' => $data->toArray(),
                    'borderColor' => $color,
                    'backgroundColor' => $color.'20',
                    'fill' => false,
                ];
            });

            return [
                'labels' => $trimestres->toArray(),
                'datasets' => $datasets->toArray(),
            ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du volume cumulé de cours: ' . $e->getMessage());
            return $this->getVolumeCumuleCoursVide('Erreur de données');
        }
    }

    /**
     * Récupérer des données vides pour le volume cumulé de cours
     */
    protected function getVolumeCumuleCoursVide(string $label = 'Aucune donnée'): array
    {
        return [
            'labels' => [$label],
            'datasets' => [
                [
                    'label' => $label,
                    'data' => [0],
                    'borderColor' => '#6b7280',
                    'backgroundColor' => '#6b728020',
                    'fill' => false
                ]
            ]
        ];
    }

    /**
     * Récupérer la couleur associée au type de cours
     */
    protected function getColorForTypeCours(string $nomType): string
    {
        $colors = [
            'présentiel' => '#3b82f6',    // Bleu
            'presentiel' => '#3b82f6',
            'cours' => '#3b82f6',         // Bleu
            'e-learning' => '#fbbf24',    // Jaune
            'elearning' => '#fbbf24',
            'workshop' => '#f97316',      // Orange
            'atelier' => '#f97316',
        ];

        return $colors[strtolower($nomType)] ?? '#6b7280'; // Gris par défaut
    }

    /**
     * Récupérer la couleur associée au taux de présence
     */
    protected function getCouleurTaux(float $taux): string
    {
        if ($taux >= 70) {
            return '#1a5d1a'; // Vert foncé
        } elseif ($taux >= 50.1) {
            return '#4caf50'; // Vert clair
        } elseif ($taux >= 30.1) {
            return '#ff9800'; // Orange
        } else {
            return '#f44336'; // Rouge
        }
    }
}
