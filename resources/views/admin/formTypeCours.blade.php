@extends('layouts.admin')

@section('title', 'Ajouter un Type de Cours')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Ajouter un Type de Cours</h2>
            <a href="{{ route('admin.types-cours.index') }}">
                Liste des types de cours
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

        <form action="{{ route('admin.types-cours.store') }}" method="POST">
            @csrf
            <div>
                <label for="nom_type_cours" class="form-label">Nom du type de cours</label>
                <input type="text"
                       name="nom_type_cours"
                       id="nom_type_cours"
                       class="form-input"
                       placeholder="Entrez le type de cours (ex: présentiel, e-learning, workshop)"
                       value="{{ old('nom_type_cours') }}"
                       required>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="form-button">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer le type de cours
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
