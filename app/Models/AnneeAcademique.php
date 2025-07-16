<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnneeAcademique extends Model
{
    protected $fillable = ['annee'];

    public function semestres() { return $this->hasMany(Semestre::class); }

}
