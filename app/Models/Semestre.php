<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    protected $fillable = [
        'nom',
        'date_debut_semestre',
        'date_fin_semestre',
        'annee_academique_id'
    ];
    public function anneeAcademique() { return $this->belongsTo(AnneeAcademique::class); }

}
