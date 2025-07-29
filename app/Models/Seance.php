<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seance extends Model
{
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

    protected $casts = [
        'date_seance' => 'date',
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
    ];

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function typeCours()
    {
        return $this->belongsTo(TypeCours::class);
    }

    public function statutSeance()
    {
        return $this->belongsTo(StatutSeance::class);
    }

    public function coordinateur()
    {
        return $this->belongsTo(Coordinateur::class);
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }
}
