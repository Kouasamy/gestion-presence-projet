<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coordinateur extends Model
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
    public function seances()
    {
        return $this->hasMany(Seance::class);
    }
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }
}
