@extends('layouts.admin')

@section('title', 'Liste des Utilisateurs')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        👥 Liste des Utilisateurs
                    </h2>
                    <a href="{{ route('admin.user.create') }}" class="custom-button">
                        <i class="fas fa-plus mr-2"></i> Ajouter un utilisateur
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="custom-table">
                        <thead>
                            <tr>

                                <th>Photo</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($users) && count($users) > 0)
                                @foreach($users as $user)
                                    <tr>

                                        <td>
                                            @if($user->photo_path)
                                                <img src="{{ asset('storage/' . $user->photo_path) }}" alt="Photo de {{ $user->nom }}" class="w-10 h-10 rounded-full object-cover" />
                                            @else
                                                <span class="text-gray-400">Aucune photo</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->nom }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->role)
                                                {{ $user->role->nom_role }}
                                            @else
                                                <span class="text-gray-400">Aucun rôle</span>
                                            @endif
                                        </td>
                                        <td class="flex gap-2 items-center">
                                            <a href="{{ route('admin.user.edit', $user->id) }}"
                                               class="custom-button bg-yellow-500 hover:bg-yellow-600 text-sm px-3 py-1">
                                                <i class="fas fa-edit mr-2"></i>
                                                Modifier
                                            </a>
                                            @if($user->role && $user->role->nom_role === 'etudiant')
                                                <a href="{{ route('admin.etudiants.formAssignerParent', $user->etudiant->id) }}" class="custom-button bg-blue-500 hover:bg-blue-600 ml-2 text-sm px-3 py-1">
                                                    <i class="fas fa-user-plus mr-2"></i>
                                                    Assigner un parent
                                                </a>
                                            @endif
                                            <form action="{{ route('admin.user.destroy', $user->id) }}"
                                                  method="POST"
                                                  class="inline-block"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="custom-button bg-red-500 hover:bg-red-600 text-sm px-3 py-1">
                                                    <i class="fas fa-trash-alt mr-2"></i>
                                                    Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">Aucun utilisateur trouvé.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

