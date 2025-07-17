<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    protected $primaryKey = 'id_user';
    public $timestamps = false;

    protected $fillable = [
        'login',
        'email',
        'password',
        'id_role'
    ];

    protected $hidden = ['password'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'id_user');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'id_user');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'id_user');
    }

    public function isAdmin(): bool
    {
        return $this->role->name === 'admin';
    }
}