<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Get the user's photo URL.
     *
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }

        // Utiliser une image par défaut
       return 'https://ui-avatars.com/api/?name=' . urlencode($this->nom) . '&background=random&color=fff';
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'nom',
        'email',
        'password',
        'role_id',
        'photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role associated with the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the etudiant record associated with the user.
     */
    public function etudiant(): HasOne
    {
        return $this->hasOne(Etudiant::class);
    }

    /**
     * Get the enseignant record associated with the user.
     */
    public function enseignant(): HasOne
    {
        return $this->hasOne(Enseignant::class);
    }

    /**
     * Get the parent record associated with the user.
     */
    public function parent(): HasOne
    {
        return $this->hasOne(Parents::class);
    }

    /**
     * Get the coordinateur record associated with the user.
     */
    public function coordinateur(): HasOne
    {
        return $this->hasOne(Coordinateur::class);
    }

    /**
     * Scope a query to only include users of a given role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfRole($query, string $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('nom_role', $roleName);
        });
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role->nom_role === $roleName;
    }
}
