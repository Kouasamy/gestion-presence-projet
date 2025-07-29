<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'date_debut_semestre',
        'date_fin_semestre',
        'annees_academiques_id'
    ];
    public function anneeAcademique() {
        return $this->belongsTo(AnneeAcademique::class, 'annees_academiques_id');
    }

}
