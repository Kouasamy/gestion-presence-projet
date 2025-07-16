<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enseignant extends Model
{
    protected $fillable = [
        'user_id',
        'telephone',
        'adresse',
        'photo_path'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'enseignant_matiere');
    }
    public function seances()
    {
        return $this->hasMany(Seance::class);
    }
}
