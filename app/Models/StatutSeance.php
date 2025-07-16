<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutSeance extends Model
{
    protected $fillable = [
        'nom_seance'
    ];
    public function seances()
    {
        return $this->hasMany(Seance::class);
    }

}
