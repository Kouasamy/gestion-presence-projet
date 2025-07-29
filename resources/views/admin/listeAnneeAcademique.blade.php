@extends('layouts.admin')

@section('title', 'Liste des Années Académiques')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        📚 Liste des Années Académiques
                    </h2>
                    <a href="{{ route('admin.annees.create') }}" class="custom-button">
                        <i class="fas fa-plus mr-2"></i> Ajouter une année
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
                                
                                <th>Année</th>
                                <th>Nombre de semestres</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($annees as $annee)
                                <tr>
                                    
                                    <td>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $annee->annee }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $annee->semestres_count ?? 0 }} semestres
                                        </span>
                                    </td>
                                    <td class="flex gap-2">
                                        <a href="{{ route('admin.annees.edit', $annee->id) }}" 
                                           class="custom-button bg-yellow-500 hover:bg-yellow-600">
                                            <i class="fas fa-edit mr-2"></i>
                                            Modifier
                                        </a>
                                        <form action="{{ route('admin.annees.destroy', $annee->id) }}" 
                                              method="POST" 
                                              class="inline-block"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette année académique ?')">
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
