<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinateur extends Model
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
    public function seances()
    {
        return $this->hasMany(Seance::class);
    }
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }
}
