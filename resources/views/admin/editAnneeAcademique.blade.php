@extends('layouts.admin')

@section('title', 'Modifier une année académique')

@section('content')
    <div class="form-wrapper">
    <div class="form-container">
        <a href="{{ route('admin.annees.index') }}" class="student-list-link">
            Liste des années académiques
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

        <form action="{{ route('admin.annees.update', $annee->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="annee" class="block text-sm font-medium text-white mb-2">
                    Année académique
                </label>
                <input type="text" 
                       name="annee" 
                       id="annee"
                       value="{{ old('annee', $annee->annee) }}" 
                       placeholder="Ex: 2024-2025" 
                       required 
                       class="w-full px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-blue-500" />
                
                @error('annee')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center">
                <button type="submit" class="custom-button">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour l'année
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
