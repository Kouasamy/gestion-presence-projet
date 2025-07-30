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

                @if($emploisDuTemps->isEmpty())
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créé par</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($emploisDuTemps as $emploi)
                                    <tr>
                                        <td class="px-6 py-4">
                                            {{ $emploi->classe->nom_classe }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $emploi->coordinateur->user->nom }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium space-x-2">
                                            <a href="{{ route('coordinateur.emploiDuTemps.show', $emploi->classe_id) }}?date_debut={{ $emploi->date_debut }}"
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                            <a href="{{ route('coordinateur.emploiDuTemps.edit', $emploi->classe_id) }}?date_debut={{ $emploi->date_debut }}"
                                               class="text-yellow-600 hover:text-yellow-900">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                        <form action="{{ route('coordinateur.emploiDuTemps.destroy', $emploi->classe_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet emploi du temps ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $emploisDuTemps->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
