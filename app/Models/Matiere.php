<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    protected $fillable = [
        'nom_matiere'
    ];
    public function enseignants()
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_matiere');
    }
    public function etudiants()
    {
        return $this->belongsToMany(Etudiant::class, 'etudiant_matiere');
    }
    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'classe_matiere');
    }
    public function seances()
    {
        return $this->hasMany(Seance::class);
    }
}
