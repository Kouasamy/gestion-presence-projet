@extends('layouts.parent')

@section('title', 'Emploi du Temps')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Emploi du Temps de vos Enfants</h1>

    @forelse ($etudiants as $etudiant)
        <div class="mb-8 bg-white rounded shadow p-4">
            <h2 class="text-xl font-semibold mb-2">{{ $etudiant->user->nom ?? 'N/A' }}</h2>
            @forelse ($etudiant->classes as $classe)
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-2">Classe : {{ $classe->nom_classe ?? 'N/A' }}</h3>
                    @if ($classe->seances->isEmpty())
                        <p class="text-gray-600">Aucun emploi du temps disponible pour cette classe.</p>
                    @else
                        <table class="w-full border border-gray-300">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="border border-gray-300 px-2 py-1">Date</th>
                                    <th class="border border-gray-300 px-2 py-1">Heure Début</th>
                                    <th class="border border-gray-300 px-2 py-1">Heure Fin</th>
                                    <th class="border border-gray-300 px-2 py-1">Matière</th>
                                    <th class="border border-gray-300 px-2 py-1">Enseignant</th>
                                    <th class="border border-gray-300 px-2 py-1">Type de Cours</th>
                                    <th class="border border-gray-300 px-2 py-1">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classe->seances as $seance)
                                    <tr>
                                        <td class="border border-gray-300 px-2 py-1">{{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}</td>
                                        <td class="border border-gray-300 px-2 py-1">{{ \Carbon\Carbon::parse($seance->heure_debut)->format('H:i') }}</td>
                                        <td class="border border-gray-300 px-2 py-1">{{ \Carbon\Carbon::parse($seance->heure_fin)->format('H:i') }}</td>
                                        <td class="border border-gray-300 px-2 py-1">{{ $seance->matiere->nom_matiere ?? 'N/A' }}</td>
                                        <td class="border border-gray-300 px-2 py-1">{{ $seance->enseignant->user->nom ?? 'N/A' }}</td>
                                        <td class="border border-gray-300 px-2 py-1">{{ $seance->typeCours->nom_type_cours ?? 'N/A' }}</td>
                                        <td class="border border-gray-300 px-2 py-1">
                                            @if($seance->statutSeance)
                                                {{ $seance->statutSeance->nom_seance }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @empty
                <p class="text-gray-600">Aucune classe assignée à cet enfant.</p>
            @endforelse
        </div>
    @empty
        <p class="text-gray-600">Aucun enfant associé trouvé.</p>
    @endforelse
</div>
@endsection
