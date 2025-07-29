@extends('layouts.admin')

@section('title', 'Modifier une classe')

@section('content')
    <div class="form-wrapper">
    <div class="form-container">
        <a href="{{ route('admin.classes.index') }}" class="student-list-link">
            Liste des classes
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

        <form action="{{ route('admin.classes.update', $classe->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nom_classe" class="block text-sm font-medium text-white mb-2">
                    Nom de la classe
                </label>
                <input type="text" 
                       name="nom_classe" 
                       id="nom_classe"
                       value="{{ old('nom_classe', $classe->nom_classe) }}" 
                       placeholder="Entrez le nom de la classe" 
                       required 
                       class="w-full px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-blue-500" />
                
                @error('nom_classe')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center">
                <button type="submit" class="custom-button">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour la classe
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
