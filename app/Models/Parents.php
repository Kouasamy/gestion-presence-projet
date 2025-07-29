<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parents extends Model
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
    public function etudiants()
{
    return $this->belongsToMany(Etudiant::class, 'parent_etudiant', 'parent_id', 'etudiant_id');
}

}
