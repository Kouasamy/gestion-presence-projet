@extends('layouts.admin')

@section('title', 'Ajouter un utilisateur')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Ajouter un utilisateur</h2>
            <a href="{{ route('admin.user.index') }}" class="text-white opacity-80 hover:opacity-100">
                Liste des utilisateurs
                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <form action="{{ route('admin.user.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <input type="text"
                       name="nom"
                       class="form-input"
                       placeholder="Nom"
                       value="{{ old('nom') }}"
                       required>
            </div>

            <div>
                <input type="email"
                       name="email"
                       class="form-input"
                       placeholder="Email"
                       value="{{ old('email') }}"
                       required>
            </div>

            <div>
                <input type="password"
                       name="password"
                       class="form-input"
                       placeholder="Mot de passe"
                       required>
            </div>

            <div>
                <input type="password"
                       name="password_confirmation"
                       class="form-input"
                       placeholder="Confirmer le mot de passe"
                       required>
            </div>

            <div>
                <select name="role_id" class="form-input" required>
                    <option value="">Sélectionner le Rôle</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->nom_role }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <input type="file"
                       name="photo_path"
                       class="form-input file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                       accept="image/*"
                       placeholder="Sélectionner la photo de l'utilisateur">
            </div>

            <button type="submit" class="form-button">
                Ajouter un utilisateur
            </button>
        </form>
    </div>
</div>
@endsection
