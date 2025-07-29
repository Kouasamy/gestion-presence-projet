@extends('layouts.parent')

@section('title', 'Absences')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Absences de vos Enfants</h1>


    @foreach ($absencesData as $data)
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-2 text-green-700">Absences Justifiées pour {{ $data['etudiant']->user->nom }}</h2>
            @if($data['absencesJustifiees']->isEmpty())
                <p class="text-gray-600">Aucune absence justifiée.</p>
            @else
                <table class="w-full border border-gray-300 mb-4">
                    <thead>
                        <tr class="bg-green-100">
                            <th class="border border-gray-300 px-2 py-1">Date</th>
                            <th class="border border-gray-300 px-2 py-1">Heure</th>
                            <th class="border border-gray-300 px-2 py-1">Matière</th>
                            <th class="border border-gray-300 px-2 py-1">Type de Cours</th>
                            <th class="border border-gray-300 px-2 py-1">Motif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['absencesJustifiees'] as $absence)
                            <tr>
                                <td class="border border-gray-300 px-2 py-1">{{ \Carbon\Carbon::parse($absence->seance->date_seance)->format('d/m/Y') }}</td>
                                <td class="border border-gray-300 px-2 py-1">{{ $absence->seance->heure_debut }} - {{ $absence->seance->heure_fin }}</td>
                                <td class="border border-gray-300 px-2 py-1">{{ $absence->seance->matiere->nom_matiere ?? 'N/A' }}</td>
                                <td class="border border-gray-300 px-2 py-1">{{ $absence->seance->typeCours->nom_type_cours ?? 'N/A' }}</td>
                                <td class="border border-gray-300 px-2 py-1">{{ $absence->justificationAbsence->motif ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div>
            <h2 class="text-xl font-semibold mb-2 text-red-700">Absences Non Justifiées pour {{ $data['etudiant']->user->nom }}</h2>
            @if($data['absencesNonJustifiees']->isEmpty())
                <p class="text-gray-600">Aucune absence non justifiée.</p>
            @else
                <table class="w-full border border-gray-300 mb-4">
                    <thead>
                        <tr class="bg-red-100">
                            <th class="border border-gray-300 px-2 py-1">Date</th>
                            <th class="border border-gray-300 px-2 py-1">Heure</th>
                            <th class="border border-gray-300 px-2 py-1">Matière</th>
                            <th class="border border-gray-300 px-2 py-1">Type de Cours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['absencesNonJustifiees'] as $absence)
                            <tr>
                                <td class="border border-gray-300 px-2 py-1">{{ \Carbon\Carbon::parse($absence->seance->date_seance)->format('d/m/Y') }}</td>
                                <td class="border border-gray-300 px-2 py-1">{{ $absence->seance->heure_debut }} - {{ $absence->seance->heure_fin }}</td>
                                <td class="border border-gray-300 px-2 py-1">{{ $absence->seance->matiere->nom_matiere ?? 'N/A' }}</td>
                                <td class="border border-gray-300 px-2 py-1">{{ $absence->seance->typeCours->nom_type_cours ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach
</div>
@endsection
