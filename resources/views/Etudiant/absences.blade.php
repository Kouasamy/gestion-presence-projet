@extends('layouts.etudiant')
@section('title','Mes absences')

@section('content')
<div class="dashboard-container px-4 py-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">📅 Mes absences</h2>

    @if($absences->isEmpty())
        <p class="text-gray-600">Aucune absence enregistrée.</p>
    @else
        <div class="space-y-4">
            @foreach($absences as $absence)
                <div class="dashboard-card flex justify-between items-center bg-white shadow-md rounded-lg px-4 py-3 border border-gray-200">
                    <div class="text-gray-700">
                        <strong>{{ $absence->seance->matiere->nom_matiere }}</strong>
                        <span class="ml-2 text-sm text-gray-500">({{ \Carbon\Carbon::parse($absence->seance->date_seance)->format('d/m/Y') }})</span>
                    </div>
                    @if($absence->justificationAbsence)
                        <span class="text-green-600 font-semibold">Justifiée</span>
                    @else
                        <span class="text-red-600 font-semibold">Non justifiée</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
