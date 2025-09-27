<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueReport extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'historique_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'seance_id',
        'date_initiale',
        'heure_debut_initiale',
        'heure_fin_initiale',
        'coordinateur_id',
        'motif',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_initiale' => 'date',
        'heure_debut_initiale' => 'datetime',
        'heure_fin_initiale' => 'datetime',
    ];

    /**
     * Get the seance that owns the historique report.
     */
    public function seance(): BelongsTo
    {
        return $this->belongsTo(Seance::class);
    }

    /**
     * Get the coordinateur that owns the historique report.
     */
    public function coordinateur(): BelongsTo
    {
        return $this->belongsTo(Coordinateur::class);
    }
}
