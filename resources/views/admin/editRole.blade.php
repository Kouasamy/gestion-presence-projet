@extends('layouts.admin')

@section('title', 'Modifier un rôle')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Modifier un rôle</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.role.index') }}">
                    Liste des rôles
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

        <form action="{{ route('admin.role.update', $role->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div>
                <label for="nom_role" class="form-label">Nom du rôle</label>
                <input type="text"
                       name="nom_role"
                       id="nom_role"
                       value="{{ old('nom_role', $role->nom_role) }}"
                       placeholder="Entrez le nom du rôle"
                       required
                       class="form-input">

                @error('nom_role')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="form-button">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour le rôle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
