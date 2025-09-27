<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    protected $fillable = ['nom_classe'];

    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'classe_matiere');
    }

    public function seances()
    {
        return $this->hasMany(Seance::class);
    }

    public function etudiants()
    {
        return $this->belongsToMany(Etudiant::class, 'etudiant_classe')
            ->withPivot('annee_academique_id', 'date_debut', 'date_fin');
    }

    public function anneeAcademique()
    {
        return $this->belongsToMany(AnneeAcademique::class, 'etudiant_classe', 'classe_id', 'annee_academique_id')
            ->distinct();
    }
}
