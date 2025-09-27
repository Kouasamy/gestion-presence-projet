@extends('layouts.admin')

@section('title', 'Modifier une classe')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Modifier une classe</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.classes.index') }}">
                    Liste des classes
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

        <form action="{{ route('admin.classes.update', $classe->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div>
                <label for="nom_classe" class="form-label">Nom de la classe</label>
                <input type="text"
                       name="nom_classe"
                       id="nom_classe"
                       value="{{ old('nom_classe', $classe->nom_classe) }}"
                       placeholder="Entrez le nom de la classe"
                       required
                       class="form-input">

                @error('nom_classe')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="form-button">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour la classe
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
