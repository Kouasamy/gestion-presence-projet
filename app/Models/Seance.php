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
        'coordonateur_id',
        'date',
        'heure_debut',
        'heure_fin',
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
