<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Presence extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'seance_id',
        'etudiant_id',
        'statut_presence_id',
        'coordinateur_id',
    ];

    /**
     * Get the seance that owns the presence.
     */
    public function seance(): BelongsTo
    {
        return $this->belongsTo(Seance::class);
    }

    /**
     * Get the etudiant that owns the presence.
     */
    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }

    /**
     * Get the statut de presence that owns the presence.
     */
    public function statutPresence(): BelongsTo
    {
        return $this->belongsTo(StatutPresence::class);
    }

    /**
     * Get the coordinateur that owns the presence.
     */
    public function coordinateur(): BelongsTo
    {
        return $this->belongsTo(Coordinateur::class);
    }


    /**
     * Get the justification d'absence for the presence.
     */
    public function justificationAbsence(): HasOne
    {
        return $this->hasOne(JustificationAbsence::class);
    }

    /**
     * Scope a query to only include presences with a specific statut.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatut($query, string $statutNom)
    {
        return $query->whereHas('statutPresence', function ($q) use ($statutNom) {
            $q->where('nom_statut_presence', $statutNom);
        });
    }

    /**
     * Scope a query to only include presences for a specific etudiant.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForEtudiant($query, int $etudiantId)
    {
        return $query->where('etudiant_id', $etudiantId);
    }

    /**
     * Scope a query to only include presences for a specific seance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSeance($query, int $seanceId)
    {
        return $query->where('seance_id', $seanceId);
    }

    /**
     * Scope a query to only include presences for a specific date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDate($query, string $date)
    {
        return $query->whereHas('seance', function ($q) use ($date) {
            $q->whereDate('date_seance', $date);
        });
    }

    /**
     * Scope a query to only include presences for a specific date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDates($query, string $startDate, string $endDate)
    {
        return $query->whereHas('seance', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date_seance', [$startDate, $endDate]);
        });
    }

    /**
     * Scope a query to only include absences.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAbsences($query)
    {
        return $query->withStatut('absent');
    }

    /**
     * Scope a query to only include justified absences.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeJustifiedAbsences($query)
    {
        return $query->absences()->whereHas('justificationAbsence');
    }

    /**
     * Scope a query to only include unjustified absences.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnjustifiedAbsences($query)
    {
        return $query->absences()->whereDoesntHave('justificationAbsence');
    }

    /**
     * Scope a query to only include presences for a specific matiere.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForMatiere($query, int $matiereId)
    {
        return $query->whereHas('seance', function ($q) use ($matiereId) {
            $q->where('matiere_id', $matiereId);
        });
    }

    /**
     * Scope a query to only include presences for a specific classe.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForClasse($query, int $classeId)
    {
        return $query->whereHas('seance', function ($q) use ($classeId) {
            $q->where('classe_id', $classeId);
        });
    }
}
