<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    protected $fillable = [
        'user_id',
        'photo_path'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'etudiant_matiere');
    }
    public function parents()
    {
        return $this->belongsToMany(Parent::class, 'parent_etudiant');
    }
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }
}
