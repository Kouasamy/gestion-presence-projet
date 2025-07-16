<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutPresence extends Model
{
    protected $fillable = [
        'nom_statut_presence'
    ];
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }


}
