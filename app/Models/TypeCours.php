<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeCours extends Model
{
    protected $fillable = [
        'nom_type_cours'
    ];
    public function seances()
    {
        return $this->hasMany(Seance::class);
    }




}
