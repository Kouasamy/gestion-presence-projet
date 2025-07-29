<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutSeance extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom_seance'
    ];
    public function seances()
    {
        return $this->hasMany(Seance::class, 'statut_seance_id');
    }

}
