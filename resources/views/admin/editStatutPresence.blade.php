@extends('layouts.admin')

@section('title', 'Modifier un statut de présence')

@section('content')
<div class="form-wrapper">
    <div class="form-container">
        <a href="{{ route('admin.statut-presences.index') }}" class="student-list-link">
            Liste des statuts de présence
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
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

        <form action="{{ route('admin.statut-presences.update', $statutPresence->id) }}" method="POST" novalidate>
    @csrf
    @method('PUT')

            <div class="mb-4">
                <label for="nom_statut_presence" class="block text-sm font-medium text-white mb-2">
                    Nom du statut de présence
                </label>
                <input type="text" 
                       name="nom_statut_presence" 
                       id="nom_statut_presence"
                       value="{{ old('nom_statut_presence', $statutPresence->nom_statut_presence) }}" 
                       placeholder="Entrez le nom du statut" 
                       required 
                       class="w-full px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-blue-500" />

    @error('nom_statut_presence')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
            </div>

            <div class="flex justify-center">
                <button type="submit" class="custom-button">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour
                </button>
            </div>
</form>
    </div>
</div>
@endsection
