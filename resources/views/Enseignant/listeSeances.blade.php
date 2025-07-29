@extends('layouts.enseignant')

@section('title', 'Mes séances')

@section('content')
<div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Mes séances</h2>

        <div class="mb-6">
            <form method="GET" action="{{ route('enseignant.listeSeances') }}" class="flex space-x-4 items-center">
                <label for="filtre" class="font-medium text-gray-700">Filtrer :</label>
                <select name="filtre" id="filtre" class="border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()">
                    <option value="" {{ request('filtre') == '' ? 'selected' : '' }}>Tous</option>
                    <option value="avenir" {{ request('filtre') == 'avenir' ? 'selected' : '' }}>À venir</option>
                    <option value="passe" {{ request('filtre') == 'passe' ? 'selected' : '' }}>Passées</option>
                </select>
            </form>
        </div>

        @if($seances->isEmpty())
            <p class="text-gray-600">Aucune séance trouvée.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type de cours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($seances as $seance)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($seance->heure_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($seance->heure_fin)->format('H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $seance->classe->nom_classe ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $seance->matiere->nom_matiere ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $seance->typeCours->nom_type_cours ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($seance->presences->isNotEmpty())
                                {{ $seance->presences->first()->statutPresence->nom_statut_presence ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('enseignant.formulairePresence', $seance->id) }}" class="text-blue-600 hover:text-blue-900 font-semibold">Saisir présences</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $seances->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
