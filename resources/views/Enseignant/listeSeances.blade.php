@extends('layouts.enseignant')

@section('title', 'Mes séances')

@section('content')
<div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-chalkboard-teacher mr-2"></i>Mes séances
            </h2>

            <div class="flex space-x-2">
                <a href="{{ route('enseignant.listeSeances') }}"
                   class="px-4 py-2 rounded-lg {{ request('filtre') == '' ? 'bg-[#e11d48] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Tous
                </a>
                <a href="{{ route('enseignant.listeSeances', ['filtre' => 'avenir']) }}"
                   class="px-4 py-2 rounded-lg {{ request('filtre') == 'avenir' ? 'bg-[#e11d48] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    À venir
                </a>
                <a href="{{ route('enseignant.listeSeances', ['filtre' => 'passe']) }}"
                   class="px-4 py-2 rounded-lg {{ request('filtre') == 'passe' ? 'bg-[#e11d48] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Passées
                </a>
            </div>
        </div>

        @if($seances->isEmpty())
            <div class="text-center py-8">
                <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500">Aucune séance trouvée pour les critères sélectionnés</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border enseignant-seances-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Heure</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Classe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Matière</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Statut</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($seances as $seance)
                        @php
                            $dateSeance = \Carbon\Carbon::parse($seance->date_seance);
                            $maintenant = \Carbon\Carbon::now();
                            $estPassee = $dateSeance->format('Y-m-d') < $maintenant->format('Y-m-d') ||
                                        ($dateSeance->format('Y-m-d') == $maintenant->format('Y-m-d') &&
                                         \Carbon\Carbon::parse($seance->heure_fin)->format('H:i:s') < $maintenant->format('H:i:s'));
                            $estCourante = $dateSeance->format('Y-m-d') == $maintenant->format('Y-m-d') &&
                                          \Carbon\Carbon::parse($seance->heure_debut)->format('H:i:s') <= $maintenant->format('H:i:s') &&
                                          \Carbon\Carbon::parse($seance->heure_fin)->format('H:i:s') >= $maintenant->format('H:i:s');
                            $estFuture = $dateSeance->format('Y-m-d') > $maintenant->format('Y-m-d') ||
                                        ($dateSeance->format('Y-m-d') == $maintenant->format('Y-m-d') &&
                                         \Carbon\Carbon::parse($seance->heure_debut)->format('H:i:s') > $maintenant->format('H:i:s'));

                            // Vérifier si la saisie des présences est possible (2 semaines pour les cours présentiels)
                            $typeCours = strtolower($seance->typeCours->nom_type_cours ?? '');
                            $saisiePresencesPossible = $estPassee || $estCourante;

                            if ($typeCours === 'cours') {
                                $dateLimit = \Carbon\Carbon::now()->subWeeks(2);
                                if ($dateSeance->lt($dateLimit)) {
                                    $saisiePresencesPossible = false;
                                }
                            }

                            // Vérifier si des présences ont déjà été saisies
                            $presencesSaisies = $seance->presences->isNotEmpty();
                        @endphp
                        <tr class="{{ $estCourante ? 'bg-green-50' : ($estPassee ? 'bg-gray-50' : 'bg-blue-50') }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border-r" data-label="Date">
                                <div class="flex items-center gap-2">
                                    @if($estPassee)
                                        <span class="inline-block w-3 h-3 bg-gray-400 rounded-full"></span>
                                    @elseif($estCourante)
                                        <span class="inline-block w-3 h-3 bg-green-500 rounded-full"></span>
                                    @else
                                        <span class="inline-block w-3 h-3 bg-blue-500 rounded-full"></span>
                                    @endif
                                    {{ $dateSeance->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $dateSeance->locale('fr')->dayName }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm border-r" data-label="Heure">
                                {{ \Carbon\Carbon::parse($seance->heure_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($seance->heure_fin)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm border-r" data-label="Classe">{{ $seance->classe->nom_classe ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm border-r" data-label="Matière">{{ $seance->matiere->nom_matiere ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm border-r" data-label="Type">{{ $seance->typeCours->nom_type_cours ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm border-r" data-label="Statut">
                                @if($estPassee)
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-800">Passée</span>
                                @elseif($estCourante)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-200 text-green-800">En cours</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-200 text-blue-800">À venir</span>
                                @endif

                                @if($presencesSaisies)
                                    <span class="ml-2 px-2 py-1 text-xs rounded-full bg-green-200 text-green-800">
                                        Présences saisies
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center" data-label="Actions">
                                @if($saisiePresencesPossible && $typeCours !== 'workshop' && $typeCours !== 'e-learning')
                                    <a href="{{ route('enseignant.formulairePresence', $seance->id) }}"
                                       class="px-3 py-1 bg-[#e11d48] text-white rounded hover:bg-[#be123c] transition-colors"
                                       onclick="event.preventDefault(); window.location.href='{{ route('enseignant.formulairePresence', $seance->id) }}';">
                                        <i class="fas fa-clipboard-check mr-1"></i>{{ $presencesSaisies ? 'Modifier présences' : 'Saisir présences' }}
                                    </a>
                                @elseif($saisiePresencesPossible && ($typeCours === 'workshop' || $typeCours === 'e-learning'))
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>Pas de présences pour ce type
                                    </span>
                                @elseif($estPassee && $typeCours !== 'workshop' && $typeCours !== 'e-learning')
                                    <span class="text-xs text-gray-500">Délai dépassé (2 semaines)</span>
                                @elseif($estFuture)
                                    <span class="text-xs text-gray-500">Séance à venir</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $seances->withQueryString()->links() }}
            </div>

            <div class="mt-4 flex space-x-4">
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-gray-400 mr-2"></span>
                    <span class="text-sm text-gray-600">Passée</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                    <span class="text-sm text-gray-600">En cours</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                    <span class="text-sm text-gray-600">À venir</span>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
