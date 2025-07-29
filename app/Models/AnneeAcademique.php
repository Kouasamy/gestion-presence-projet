<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnneeAcademique extends Model
{
    protected $fillable = ['annee'];
     protected $table = 'annees_academiques';

    public function semestres() {
        return $this->hasMany(Semestre::class, 'annees_academiques_id');
    }

}
