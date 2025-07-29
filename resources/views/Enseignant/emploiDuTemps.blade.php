@extends('layouts.enseignant')

@section('title', 'Mon emploi du temps')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Mon emploi du temps</h2>

            @if($seances->isEmpty())
                <p class="text-gray-600">Aucune séance programmée pour le moment.</p>
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
                            <td class="px-6 py-4 whitespace-nowrap">{{ $seance->statutSeance->nom_seance ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
