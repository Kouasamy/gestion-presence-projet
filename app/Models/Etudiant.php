<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'photo_path'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'etudiant_matiere', 'etudiant_id', 'matiere_id');
    }
   public function parents()
{
    return $this->belongsToMany(Parents::class, 'parent_etudiant', 'etudiant_id', 'parent_id');
}    public function presences()
    {
        return $this->hasMany(Presence::class);
    }
    public function classes()
{
    return $this->belongsToMany(Classe::class, 'etudiant_classe', 'etudiant_id', 'classe_id')->withPivot('annee_academique_id');
    }
}
