@extends('layouts.admin')

@section('title', 'Modifier un semestre')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Modifier un semestre</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.semestres.index') }}">
                    Liste des semestres
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
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

        <form action="{{ route('admin.semestres.update', $semestre->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nom" class="form-label">Nom du semestre</label>
                    <input type="text"
                           name="nom"
                           id="nom"
                           value="{{ old('nom', $semestre->nom) }}"
                           placeholder="Nom du semestre (ex: Semestre 1)"
                           required
                           class="form-input">

                    @error('nom')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="annee_academique_id" class="form-label">Année académique</label>
                    <select name="annee_academique_id" id="annee_academique_id" class="form-input" required>
                        <option value="">Sélectionnez une année académique</option>
                        @foreach($annees as $annee)
                            <option value="{{ $annee->id }}" {{ old('annee_academique_id', $semestre->annee_academique_id) == $annee->id ? 'selected' : '' }}>
                                {{ $annee->annee }}
                            </option>
                        @endforeach
                    </select>

                    @error('annee_academique_id')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="date_debut_semestre" class="form-label">Date de début</label>
                    <input type="date"
                           name="date_debut_semestre"
                           id="date_debut_semestre"
                           value="{{ old('date_debut_semestre', $semestre->date_debut_semestre) }}"
                           required
                           class="form-input">

                    @error('date_debut_semestre')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_fin_semestre" class="form-label">Date de fin</label>
                    <input type="date"
                           name="date_fin_semestre"
                           id="date_fin_semestre"
                           value="{{ old('date_fin_semestre', $semestre->date_fin_semestre) }}"
                           required
                           class="form-input">

                    @error('date_fin_semestre')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="form-button">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour le semestre
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
