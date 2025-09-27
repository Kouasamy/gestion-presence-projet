@extends('layouts.admin')

@section('title', 'Liste des Cours')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        📚 Liste des Cours
                    </h2>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.cours.create') }}" class="custom-button">
                            <i class="fas fa-plus mr-2"></i> Ajouter un cours
                        </a>
                        <a href="{{ route('admin.types-cours.index') }}" class="custom-button bg-purple-500 hover:bg-purple-600">
                            <i class="fas fa-list mr-2"></i> Voir les types de cours
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="custom-table w-full admin-cours-table">
                        <thead>
                            <tr>
                                <th>Nom du cours</th>
                                <th>Type de cours</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cours as $c)
                                <tr>
                                    <td data-label="Nom du cours">{{ $c->matiere->nom_matiere }}</td>
                                    <td data-label="Type de cours">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $c->typeCours->nom_type_cours ?? 'Non défini' }}
                                        </span>
                                    </td>
                                    <td data-label="Description">{{ Str::limit($c->description, 50) }}</td>
                                    <td data-label="Actions" class="flex gap-2">
                                        <a href="{{ route('admin.cours.edit', $c->id) }}"
                                           class="custom-button bg-yellow-500 hover:bg-yellow-600">
                                            <i class="fas fa-edit mr-2"></i>
                                            Modifier
                                        </a>
                                        <form action="{{ route('admin.cours.destroy', $c->id) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="custom-button bg-red-500 hover:bg-red-600">
                                                <i class="fas fa-trash-alt mr-2"></i>
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
