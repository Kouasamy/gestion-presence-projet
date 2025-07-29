@extends('layouts.admin')

@section('title', 'Modifier un utilisateur')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Modifier un utilisateur</h2>
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

        <form action="{{ route('admin.user.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="nom" class="form-label">Nom</label>
                <input type="text" name="nom" id="nom" class="form-input" value="{{ old('nom', $user->nom) }}" required>
            </div>
            <div>
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email) }}" required>
            </div>
            <div>
                <label for="role_id" class="form-label">Rôle</label>
                <select name="role_id" id="role_id" class="form-input" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->nom_role }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="photo_path" class="form-label">Photo</label>
                @if($user->photo_path)
                    <img src="{{ asset('storage/' . $user->photo_path) }}" alt="Photo actuelle" class="w-20 h-20 rounded-full object-cover mb-2" />
                @endif
                <input type="file" name="photo_path" id="photo_path" class="form-input">
            </div>
            <button type="submit" class="form-button">Mettre à jour</button>
        </form>
    </div>
</div>
@endsection 