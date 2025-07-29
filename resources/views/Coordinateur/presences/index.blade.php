@extends('layouts.coordinateur')

@section('title', 'Gestion des présences')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestion des présences</h1>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('coordinateur.presences.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="classe" class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe" id="classe" class="form-select w-full rounded-md border-gray-300">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom_classe }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}" 
                    class="form-input w-full rounded-md border-gray-300">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des séances -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="min-w-full divide-y divide-gray-200">
            <div class="bg-gray-50">
                <div class="grid grid-cols-6 gap-4 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <div>Date</div>
                    <div>Heure</div>
                    <div>Classe</div>
                    <div>Présences saisies</div>
                    <div>Absences</div>
                    <div>Actions</div>
                </div>
            </div>
            <div class="bg-white divide-y divide-gray-200">
                @forelse($seances as $seance)
                    <div class="grid grid-cols-6 gap-4 px-6 py-4 text-sm text-gray-900">
                        <div>{{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}</div>
                        <div>{{ \Carbon\Carbon::parse($seance->heure_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($seance->heure_fin)->format('H:i') }}</div>
                        <div>{{ $seance->classe->nom_classe }}</div>
                        <div>
                            @php
                                $totalEtudiants = $seance->classe->etudiants->count();
                                $presencesSaisies = $seance->presences->count();
                            @endphp
                            <span class="px-2 py-1 text-sm rounded-full {{ $presencesSaisies == $totalEtudiants ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $presencesSaisies }}/{{ $totalEtudiants }}
                            </span>
                        </div>
                        <div>
                            @php
                                $absences = $seance->presences->where('statut_presence_id', 2)->count();
                            @endphp
                            <span class="px-2 py-1 text-sm rounded-full {{ $absences > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $absences }}
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('coordinateur.presences.form', $seance->id) }}" 
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-clipboard-list mr-1"></i>
                                {{ $presencesSaisies == 0 ? 'Saisir' : 'Modifier' }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-4 text-center text-gray-500">
                        Aucune séance trouvée pour les critères sélectionnés.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $seances->links() }}
    </div>
</div>
@endsection