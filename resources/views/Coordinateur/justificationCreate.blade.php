@extends('layouts.coordinateur')

@section('title', 'Justifier une absence')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Justifier une absence</h2>
            <a href="{{ route('coordinateur.seances.presences.form', $presence->seance_id) }}" class="text-white opacity-80 hover:opacity-100">
                Retour aux présences
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
                    <p class="font-semibold">{{ $presence->etudiant->user->nom }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Classe</p>
                    <p class="font-semibold">{{ $presence->seance->classe->nom_classe }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Date de la séance</p>
                    <p class="font-semibold">{{ \Carbon\Carbon::parse($presence->seance->date_seance)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Matière</p>
                    <p class="font-semibold">{{ $presence->seance->matiere->nom_matiere }}</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="form-error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="form-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('coordinateur.justification.store', $presence->id) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <div>
                <label for="motif" class="form-label">Motif de l'absence</label>
                <textarea name="motif"
                          id="motif"
                          class="w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50"
                          rows="4"
                          placeholder="Entrez le motif de l'absence"
                          required>{{ old('motif') }}</textarea>
            </div>

            <div>
                <label for="document" class="form-label">Document justificatif (optionnel)</label>
                <input type="file"
                       name="document"
                       id="document"
                       class="w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100"
                       accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-[#e11d48] text-white rounded-lg hover:bg-[#be123c] transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer la justification
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
