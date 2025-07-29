@extends('layouts.etudiant')

@section('title', 'Tableau de bord')

@section('content')
<div class="dashboard-container px-4 py-6 max-w-7xl mx-auto">
    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <section class="dashboard-card stat-card bg-white rounded-lg shadow p-6">
            <h3 class="stat-card-title text-lg font-semibold mb-2">Mon taux de présence global</h3>
            <div class="stat-card-value blue">
    {{ number_format($tauxGlobal ?? 0, 1) }}%
</div>
            <p class="text-sm text-gray-500 mt-1">Tous cours confondus</p>
        </section>

        <section class="dashboard-card stat-card bg-white rounded-lg shadow p-6">
            <h3 class="stat-card-title text-lg font-semibold mb-2">Nombre d'absences non justifiées</h3>
            <div class="stat-card-value red">
    {{ $absencesNonJustifiees ?? 0 }}
</div>
            <p class="text-sm text-gray-500 mt-1">Depuis le début de l'année</p>
        </section>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="dashboard-card chart-container bg-white rounded-lg shadow p-6">
            <h3 class="chart-title text-lg font-semibold mb-4 flex items-center gap-2 text-gray-800">
                <i class="fas fa-chart-bar"></i>
                <span>Taux de présence par matière</span>
            </h3>
            @if($presencesParMatiere->isEmpty())
                <p class="text-gray-500">Aucune donnée disponible.</p>
            @else
                <canvas id="chartPresenceParMatiere" aria-label="Graphique des taux de présence par matière" role="img"></canvas>
            @endif
        </section>

        <section class="dashboard-card chart-container bg-white rounded-lg shadow p-6">
            <h3 class="chart-title text-lg font-semibold mb-4 flex items-center gap-2 text-gray-800">
                <i class="fas fa-chart-line"></i>
                <span>Note d'assiduité par matière</span>
            </h3>
            @if($assiduite->isEmpty())
                <p class="text-gray-500">Aucune donnée disponible.</p>
            @else
                <canvas id="chartAssiduite" aria-label="Graphique des notes d'assiduité par matière" role="img"></canvas>
            @endif
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données
    const presenceLabels = {!! json_encode($presencesParMatiere->pluck('matiere')) !!};
    const presenceData = {!! json_encode($presencesParMatiere->pluck('taux')) !!};
    const presenceColors = {!! json_encode($presencesParMatiere->pluck('couleur')) !!};

    const assiduiteLabels = {!! json_encode($assiduite->pluck('matiere')) !!};
    const assiduiteData = {!! json_encode($assiduite->pluck('note')) !!};

    // Graphique Présence par matière
    if (presenceLabels.length > 0) {
        new Chart(document.getElementById('chartPresenceParMatiere'), {
            type: 'bar',
            data: {
                labels: presenceLabels,
                datasets: [{
                    label: 'Taux de présence (%)',
                    data: presenceData,
                    backgroundColor: presenceColors,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed.y + '%'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: val => val + '%'
                        }
                    }
                }
            }
        });
    }

    // Graphique Note d'assiduité
    if (assiduiteLabels.length > 0) {
        new Chart(document.getElementById('chartAssiduite'), {
            type: 'line',
            data: {
                labels: assiduiteLabels,
                datasets: [{
                    label: 'Note / 20',
                    data: assiduiteData,
                    backgroundColor: '#3b82f6',
                    borderColor: '#3b82f6',
                    tension: 0.3,
                    fill: false,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed.y + ' / 20'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                        ticks: {
                            stepSize: 2
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
