<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parents extends Model
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
    public function etudiants()
    {
        return $this->belongsToMany(Etudiant::class, 'parent_etudiant');
    }
}
