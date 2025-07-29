@extends('layouts.coordinateur')

@section('title', 'Statistiques')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800">
                            <i class="fas fa-chart-line mr-2"></i>Statistiques et graphiques
                        </h2>
                        <a href="{{ route('coordinateur.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Retour au dashboard
                        </a>
                    </div>

                    <!-- Filtres -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <form method="GET" action="{{ route('coordinateur.statistiques') }}" id="filterForm">
                            <div class="flex flex-wrap gap-4">
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Année académique</label>
                                    <select name="annee" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white text-gray-900">
                                        <option value="">Toutes les années</option>
                                        @foreach($annees as $annee)
                                            <option value="{{ $annee->id }}" {{ request('annee') == $annee->id ? 'selected' : '' }}>
                                                {{ $annee->annee }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Semestre</label>
                                    <select name="semestre" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white text-gray-900">
                                        <option value="">Tous les semestres</option>
                                        @foreach($semestres as $semestre)
                                            <option value="{{ $semestre->id }}" {{ request('semestre') == $semestre->id ? 'selected' : '' }}>
                                                {{ $semestre->nom}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                                    <select name="classe" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white text-gray-800">
                                        <option value="">Toutes les classes</option>
                                        @foreach($classes as $classe)
                                            <option value="{{ $classe->id }}" {{ request('classe') == $classe->id ? 'selected' : '' }}>
                                                {{ $classe->nom_classe }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" class="px-4 py-2 bg-[#e11d48] text-white rounded-lg hover:bg-[#be123c] transition-colors">
                                        <i class="fas fa-filter mr-2"></i>Filtrer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Graphiques en grille responsive -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- 🔹 1. Graphe du taux de présence par étudiant -->
                    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                            Taux de présence par étudiant
                        </h3>
                        <div class="relative h-80">
                            <canvas id="chartEtudiants"></canvas>
                        </div>
                    </div>

                    <!-- 🔹 2. Graphe du taux de présence par classe -->
                    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-chart-column mr-2 text-green-600"></i>
                            Taux de présence par classe
                        </h3>
                        <div class="relative h-80">
                            <canvas id="chartClasses"></canvas>
                        </div>
                    </div>

                    <!-- 🔹 3. Graphe du volume de cours dispensés -->
                    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-chart-area mr-2 text-purple-600"></i>
                            Volume de cours par type
                        </h3>
                        <div class="relative h-80">
                            <canvas id="chartVolumeType"></canvas>
                        </div>
                    </div>

                    <!-- 🔹 4. Graphe cumulé du volume de cours -->
                    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-chart-line mr-2 text-orange-600"></i>
                            Volume cumulé des cours
                        </h3>
                        <div class="relative h-80">
                            <canvas id="chartVolumeCumule"></canvas>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js depuis CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Configuration globale Chart.js
    Chart.defaults.font.family = "'Inter', 'system-ui', sans-serif";
    Chart.defaults.color = '#374151';
    Chart.defaults.plugins.tooltip.backgroundColor = '#1f2937';
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.padding = 12;

    // Initialisation des graphiques

    // 🔹 1. Graphe taux de présence par étudiant (barres horizontales)
    const dataEtudiants = @json($tauxPresenceEtudiants);

    new Chart(document.getElementById('chartEtudiants'), {
        type: 'bar',
        data: {
            labels: dataEtudiants.map(e => e.nom),
            datasets: [{
                label: 'Taux de présence (%)',
                data: dataEtudiants.map(e => e.taux),
                backgroundColor: dataEtudiants.map(e => e.couleur),
                borderRadius: 4,
                borderWidth: 0
            }]
        },
        options: {
            indexAxis: 'y', // Barres horizontales
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.x.toFixed(1) + '%';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                y: {
                    grid: { display: false }
                }
            }
        }
    });

    // 🔹 2. Graphe taux de présence par classe (barres verticales)
    const dataClasses = @json($tauxPresenceClasses);

    new Chart(document.getElementById('chartClasses'), {
        type: 'bar',
        data: {
            labels: dataClasses.map(c => c.classe),
            datasets: [{
                label: 'Taux de présence (%)',
                data: dataClasses.map(c => c.taux),
                backgroundColor: '#10b981',
                borderRadius: 4,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toFixed(1) + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 🔹 3. Graphe volume de cours par type (histogramme empilé)
    const dataVolumeType = @json($volumeCoursParType);

    new Chart(document.getElementById('chartVolumeType'), {
        type: 'bar',
        data: dataVolumeType,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' cours';
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    grid: { display: false }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // 🔹 4. Graphe volume cumulé (graphique linéaire)
    const dataVolumeCumule = @json($volumeCumuleCours);

    new Chart(document.getElementById('chartVolumeCumule'), {
        type: 'line',
        data: dataVolumeCumule,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' cours';
                        }
                    }
                }
            },
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            elements: {
                line: {
                    tension: 0.3
                },
                point: {
                    radius: 4,
                    hoverRadius: 6
                }
            }
        }
    });

});
</script>
@endsection
