<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id',
        'name',
        'date',
        'rating',
        'isPrivate',
        'id_user'
    ];

    protected $casts = [
        'isPrivate' => 'boolean',
        'date' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'id_material');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'id_material');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'id_material');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tags::class, 'material_tag', 'id_material', 'id_tag');
    }
}