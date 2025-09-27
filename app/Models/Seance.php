<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'matiere_id',
        'classe_id',
        'enseignant_id',
        'type_cours_id',
        'statut_seance_id',
        'coordinateur_id',
        'date_seance',
        'heure_debut',
        'heure_fin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_seance' => 'date',
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
    ];

    /**
     * Get the matiere that owns the seance.
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Get the enseignant that owns the seance.
     */
    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(Enseignant::class);
    }

    /**
     * Get the classe that owns the seance.
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Get the type de cours that owns the seance.
     */
    public function typeCours(): BelongsTo
    {
        return $this->belongsTo(TypeCours::class);
    }

    /**
     * Get the statut de seance that owns the seance.
     */
    public function statutSeance(): BelongsTo
    {
        return $this->belongsTo(StatutSeance::class);
    }

    /**
     * Get the coordinateur that owns the seance.
     */
    public function coordinateur(): BelongsTo
    {
        return $this->belongsTo(Coordinateur::class);
    }

    /**
     * Get the presences for the seance.
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Get the historique des reports for the seance.
     */
    public function historiqueReports(): HasMany
    {
        return $this->hasMany(HistoriqueReport::class);
    }

    /**
     * Scope a query to only include seances for a specific coordinateur.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCoordinateur($query, int $coordinateurId)
    {
        return $query->where('coordinateur_id', $coordinateurId);
    }

    /**
     * Scope a query to only include seances for a specific enseignant.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForEnseignant($query, int $enseignantId)
    {
        return $query->where('enseignant_id', $enseignantId);
    }

    /**
     * Scope a query to only include seances for a specific classe.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForClasse($query, int $classeId)
    {
        return $query->where('classe_id', $classeId);
    }

    /**
     * Scope a query to only include seances for a specific date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date_seance', $date);
    }

    /**
     * Scope a query to only include seances for today.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date_seance', Carbon::today());
    }

    /**
     * Scope a query to only include seances for a specific date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDates($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date_seance', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include seances without presences.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutPresences($query)
    {
        return $query->whereDoesntHave('presences');
    }

    /**
     * Scope a query to only include seances with a specific type de cours.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTypeCours($query, string $typeCours)
    {
        return $query->whereHas('typeCours', function ($q) use ($typeCours) {
            $q->where('nom_type_cours', $typeCours);
        });
    }
}
