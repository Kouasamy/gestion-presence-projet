<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etudiant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'photo_path',
    ];

    /**
     * Get the user that owns the etudiant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The matieres that belong to the etudiant.
     */
    public function matieres(): BelongsToMany
    {
        return $this->belongsToMany(Matiere::class, 'etudiant_matiere', 'etudiant_id', 'matiere_id');
    }

    /**
     * The parents that belong to the etudiant.
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(Parents::class, 'parent_etudiant', 'etudiant_id', 'parent_id');
    }

    /**
     * Get the presences for the etudiant.
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * The classes that belong to the etudiant.
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classe::class, 'etudiant_classe', 'etudiant_id', 'classe_id')
            ->withPivot('annee_academique_id');
    }

    /**
     * Scope a query to only include etudiants in a specific classe.
     */
    public function scopeInClasse(Builder $query, int $classeId): Builder
    {
        return $query->whereHas('classes', function ($q) use ($classeId) {
            $q->where('classes.id', $classeId);
        });
    }

    /**
     * Scope a query to only include etudiants for a specific annee academique.
     */
    public function scopeForAnneeAcademique(Builder $query, int $anneeId): Builder
    {
        return $query->whereHas('classes', function ($q) use ($anneeId) {
            $q->where('etudiant_classe.annee_academique_id', $anneeId);
        });
    }

    /**
     * Get absences for the etudiant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Builder
     */
    public function absences()
    {
        return $this->presences()->whereHas('statutPresence', function ($q) {
            $q->where('nom_statut_presence', 'absent');
        });
    }

    /**
     * Get justified absences for the etudiant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Builder
     */
    public function justifiedAbsences()
    {
        return $this->absences()->whereHas('justificationAbsence');
    }

    /**
     * Get unjustified absences for the etudiant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Builder
     */
    public function unjustifiedAbsences()
    {
        return $this->absences()->whereDoesntHave('justificationAbsence');
    }
}
