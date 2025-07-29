@extends('layouts.admin')

@section('title', 'Modifier un semestre')

@section('content')
    <div class="form-wrapper">
    <div class="form-container">
        <a href="{{ route('admin.semestres.index') }}" class="student-list-link">
            Liste des semestres
                <svg viewBox="0 0 24 24">
                    <path d="M10 17l5-5-5-5v10z" />
                </svg>
            </a>

            @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                </div>
            @endif

        <form action="{{ route('admin.semestres.update', $semestre->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nom" class="block text-sm font-medium text-white mb-2">
                    Nom du semestre
                </label>
                <input type="text" 
                       name="nom" 
                       id="nom"
                       value="{{ old('nom', $semestre->nom) }}" 
                       placeholder="Nom du semestre (ex: Semestre 1)" 
                       required 
                       class="w-full px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-blue-500" />
                
                @error('nom')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="date_debut_semestre" class="block text-sm font-medium text-white mb-2">
                    Date de début
                </label>
                <input type="date" 
                       name="date_debut_semestre" 
                       id="date_debut_semestre"
                       value="{{ old('date_debut_semestre', $semestre->date_debut_semestre) }}" 
                       required 
                       class="w-full px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-blue-500" />
                
                @error('date_debut_semestre')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="date_fin_semestre" class="block text-sm font-medium text-white mb-2">
                    Date de fin
                </label>
                <input type="date" 
                       name="date_fin_semestre" 
                       id="date_fin_semestre"
                       value="{{ old('date_fin_semestre', $semestre->date_fin_semestre) }}" 
                       required 
                       class="w-full px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-blue-500" />
                
                @error('date_fin_semestre')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="annee_academique_id" class="block text-sm font-medium text-white mb-2">
                    ID Année académique
                </label>
                <input type="text" 
                       name="annee_academique_id" 
                       id="annee_academique_id"
                value="{{ old('annee_academique_id', $semestre->annee_academique_id) }}"
                       placeholder="ID Année académique liée" 
                       required 
                       class="w-full px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-blue-500" />
                
                @error('annee_academique_id')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center">
                <button type="submit" class="custom-button">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour le semestre
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
