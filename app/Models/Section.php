<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    protected $fillable = [
        'text',
        'bytes',
        'url',
        'is_media',
        'position',
        'id_material'
    ];

    protected $casts = [
        'is_media' => 'boolean'
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'id_material');
    }
}