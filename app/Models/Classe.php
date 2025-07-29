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
    return $this->belongsToMany(Etudiant::class, 'etudiant_classe');
}
}
