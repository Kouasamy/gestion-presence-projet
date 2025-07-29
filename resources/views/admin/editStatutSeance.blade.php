@extends('layouts.admin')

@section('title', 'Modifier un statut de séance')

@section('content')
<div class="form-wrapper">
    <div class="form-container">
        <a href="{{ route('admin.statut-seances.index') }}" class="student-list-link">
            Liste des statuts de séance
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

        @if (session('success'))
            <div class="error-box success-box">
                {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('admin.statut-seances.update', $statut->id) }}" method="POST" novalidate>
    @csrf
    @method('PUT')

            <div class="mb-4">
                <label for="nom_seance" class="block text-sm font-medium text-white mb-2">
                    Nom de la séance
                </label>
                <input type="text"
                       name="nom_seance"
                       id="nom_seance"
                       value="{{ old('nom_seance', $statut->nom_seance) }}"
                       placeholder="Entrez le nom de la séance"
                       required
                       class="w-full px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-blue-500 text-black"  />

    @error('nom_seance')
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
