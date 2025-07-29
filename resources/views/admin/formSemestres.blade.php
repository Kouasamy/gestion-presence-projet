@extends('layouts.admin')

@section('title', 'Ajouter un semestre')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Ajouter un semestre</h2>
            <a href="{{ route('admin.semestres.index') }}">
                Liste des semestres
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
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

        <form action="{{ route('admin.semestres.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="nom" class="form-label">Nom du semestre</label>
                    <input type="text"
                           name="nom"
                           id="nom"
                           class="form-input"
                           placeholder="Entrez le nom du semestre"
                           value="{{ old('nom') }}"
                           required>
                </div>

                <div>
                    <label for="date_debut_semestre" class="form-label">Date de début</label>
                    <input type="date"
                           name="date_debut_semestre"
                           id="date_debut_semestre"
                           class="form-input"
                           value="{{ old('date_debut_semestre') }}"
                           required>
                </div>

                <div>
                    <label for="date_fin_semestre" class="form-label">Date de fin</label>
                    <input type="date"
                           name="date_fin_semestre"
                           id="date_fin_semestre"
                           class="form-input"
                           value="{{ old('date_fin_semestre') }}"
                           required>
                </div>

                <div>
                    <label for="annees_academiques_id" class="form-label">Année académique</label>
                    <select name="annees_academiques_id"
                            id="annees_academiques_id"
                            class="form-input"
                            required>
                        <option value="">Sélectionnez une année académique</option>
                        @foreach($annees as $annee)
                            <option value="{{ $annee->id }}" {{ old('annees_academiques_id') == $annee->id ? 'selected' : '' }}>
                                {{ $annee->annee }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="form-button">
                Ajouter un semestre
            </button>
        </form>
    </div>
</div>
@endsection
