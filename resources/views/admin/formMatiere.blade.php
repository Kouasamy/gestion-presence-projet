@extends('layouts.admin')

@section('title', 'Ajouter une Matière')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Ajouter une Matière</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.matieres.index') }}">
                    Liste des matières
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

        <form action="{{ route('admin.matieres.store') }}" method="POST">
            @csrf
            <div>
                <label for="nom_matiere" class="form-label">Nom de la matière</label>
                <input type="text"
                       name="nom_matiere"
                       id="nom_matiere"
                       class="form-input"
                       placeholder="Entrez le nom de la matière"
                       value="{{ old('nom_matiere') }}"
                       required>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="form-button">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer la matière
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
