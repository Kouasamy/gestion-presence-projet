<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JustificationAbsence extends Model
{
    protected $fillable = [
        'presence_id',
        'motif',
        'date_justification',
        'document_path',
    ];

    public function presence()
    {
        return $this->belongsTo(Presence::class);
    }
}
