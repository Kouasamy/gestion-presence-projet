<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enseignant extends Model
{
    use HasFactory;
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
        return $this->belongsToMany(Matiere::class, 'enseignant_matiere');
    }
    public function seances()
    {
        return $this->hasMany(Seance::class);
    }
}
