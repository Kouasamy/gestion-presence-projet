@extends('layouts.admin')

@section('title', 'Modifier un Cours')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Modifier un Cours</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.cours.index') }}">
                    Liste des cours
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

        <form action="{{ route('admin.cours.update', $cours->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nom_matiere" class="form-label">Nom du cours</label>
                    <input type="text"
                           name="nom_matiere"
                           id="nom_matiere"
                           class="form-input"
                           placeholder="Entrez le nom du cours"
                           value="{{ old('nom_matiere', $cours->matiere->nom_matiere) }}"
                           required>
                </div>

                <div>
                    <label for="type_cours_id" class="form-label">Type de cours</label>
                    <select name="type_cours_id" id="type_cours_id" class="form-input" required>
                        <option value="">Sélectionnez un type de cours</option>
                        @foreach($typesCours as $type)
                            <option value="{{ $type->id }}" {{ (old('type_cours_id', $cours->type_cours_id) == $type->id) ? 'selected' : '' }}>
                                {{ $type->nom_type_cours }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label for="description" class="form-label">Description (optionnelle)</label>
                <textarea name="description"
                          id="description"
                          class="form-input"
                          rows="3"
                          placeholder="Description du cours">{{ old('description', $cours->description) }}</textarea>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="form-button">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
