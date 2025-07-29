<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    protected $fillable = [
        'seance_id',
        'etudiant_id',
        'statut_presence_id',
        'coordinateur_id',
    ];

    public function seance()
    {
        return $this->belongsTo(Seance::class);
    }

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function statutPresence()
    {
        return $this->belongsTo(StatutPresence::class);
    }

    public function coordinateur()
    {
        return $this->belongsTo(Coordinateur::class);
    }

    public function justificationAbsence()
    {
        return $this->hasOne(JustificationAbsence::class);
    }
}
