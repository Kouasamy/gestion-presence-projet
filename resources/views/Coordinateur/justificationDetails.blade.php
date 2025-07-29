@extends('layouts.coordinateur')

@section('title', 'Détails de la justification')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Détails de la justification</h2>
            <a href="{{ route('coordinateur.absences.index') }}" class="text-white opacity-80 hover:opacity-100">
                Retour à la liste
                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <!-- Informations de l'absence -->
        <div class="bg-gray-800 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-2 gap-4 text-white">
                <div>
                    <p class="text-gray-400">Étudiant</p>
                    <p class="font-semibold">{{ $absence->etudiant->user->nom }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Classe</p>
                    <p class="font-semibold">{{ $absence->seance->classe->nom_classe }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Date de la séance</p>
                    <p class="font-semibold">{{ \Carbon\Carbon::parse($absence->seance->date_seance)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Matière</p>
                    <p class="font-semibold">{{ $absence->seance->matiere->nom_matiere }}</p>
                </div>
            </div>
        </div>

        <!-- Détails de la justification -->
        <div class="bg-white rounded-lg p-6 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Informations de la justification</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Date de justification</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($absence->justificationAbsence->date_justification)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Statut</p>
                        <p class="font-medium">
                            <span class="px-2 py-1 text-sm rounded-full bg-green-100 text-green-800">
                                Justifiée
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <p class="text-gray-600 mb-2">Motif</p>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-800">{{ $absence->justificationAbsence->motif }}</p>
                </div>
            </div>

            @if($absence->justificationAbsence->document_path)
            <div>
                <p class="text-gray-600 mb-2">Document justificatif</p>
                <a href="{{ Storage::url($absence->justificationAbsence->document_path) }}"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Voir le document
                </a>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('coordinateur.presences.form', $absence->seance_id) }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Voir la feuille de présence
            </a>
            <a href="{{ route('coordinateur.absences.index') }}"
               class="px-4 py-2 bg-[#e11d48] text-white rounded-lg hover:bg-[#be123c] transition-colors">
                Retour à la liste
            </a>
        </div>
    </div>
</div>
@endsection
