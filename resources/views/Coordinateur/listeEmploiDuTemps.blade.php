@extends('layouts.coordinateur')

@section('title', 'Liste des emplois du temps')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-calendar-alt mr-2"></i>Liste des emplois du temps
                    </h2>
                    <a href="{{ route('coordinateur.emploiDuTemps.create') }}" class="bg-[#e11d48] text-white px-4 py-2 rounded-lg hover:bg-[#be123c] transition-colors flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        Créer un emploi du temps
                    </a>
                </div>

                <!-- Filtres pour les emplois du temps -->
                <div class="flex gap-4 mb-6">
                    <a href="{{ route('coordinateur.emploiDuTemps.index') }}"
                       class="px-4 py-2 rounded-lg {{ !request()->has('filter') ? 'bg-[#2f3357] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} transition-colors">
                        Tous
                    </a>
                    <a href="{{ route('coordinateur.emploiDuTemps.index', ['filter' => 'en_cours']) }}"
                       class="px-4 py-2 rounded-lg {{ request()->input('filter') == 'en_cours' ? 'bg-[#e11d48] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} transition-colors">
                        En cours
                    </a>
                    <a href="{{ route('coordinateur.emploiDuTemps.index', ['filter' => 'a_venir']) }}"
                       class="px-4 py-2 rounded-lg {{ request()->input('filter') == 'a_venir' ? 'bg-[#2f3357] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} transition-colors">
                        À venir
                    </a>
                    <a href="{{ route('coordinateur.emploiDuTemps.index', ['filter' => 'passe']) }}"
                       class="px-4 py-2 rounded-lg {{ request()->input('filter') == 'passe' ? 'bg-[#2f3357] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} transition-colors">
                        Passés
                    </a>
                </div>

                @if(empty($emploisDuTempsCollection) || count($emploisDuTempsCollection) === 0)
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-week text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-500">Aucun emploi du temps trouvé pour les critères sélectionnés</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semaine</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($emploisDuTempsCollection as $classeId => $emploi)
                                    @foreach($emploi['semaines'] as $index => $semaine)
                                        <tr>
                                            <td class="px-6 py-4">
                                                {{ $emploi['classe'] }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    @if($semaine['est_passee'])
                                                        <span class="inline-block w-3 h-3 bg-gray-400 rounded-full"></span>
                                                    @elseif($semaine['est_courante'])
                                                        <span class="inline-block w-3 h-3 bg-green-500 rounded-full"></span>
                                                    @else
                                                        <span class="inline-block w-3 h-3 bg-blue-500 rounded-full"></span>
                                                    @endif
                                                    <span>
                                                        {{ \Carbon\Carbon::parse($semaine['debut_semaine'])->format('d/m/Y') }} -
                                                        {{ \Carbon\Carbon::parse($semaine['fin_semaine'])->format('d/m/Y') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($semaine['est_passee'])
                                                    <span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-xs">Passé</span>
                                                @elseif($semaine['est_courante'])
                                                    <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs">En cours</span>
                                                @else
                                                    <span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-xs">À venir</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium space-x-2">
                                                <a href="{{ route('coordinateur.emploiDuTemps.show', $emploi['classe_id']) }}?date_debut={{ $semaine['debut_semaine'] }}"
                                                   class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                                <a href="{{ route('coordinateur.emploiDuTemps.edit', $emploi['classe_id']) }}?date_debut={{ $semaine['debut_semaine'] }}"
                                                   class="text-yellow-600 hover:text-yellow-900">
                                                    <i class="fas fa-edit"></i> Modifier
                                                </a>
                                                <form action="{{ route('coordinateur.emploiDuTemps.destroy', $emploi['classe_id']) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet emploi du temps ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="date_debut" value="{{ $semaine['debut_semaine'] }}">
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-trash"></i> Supprimer
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination supprimée pour afficher tous les emplois du temps -->
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
