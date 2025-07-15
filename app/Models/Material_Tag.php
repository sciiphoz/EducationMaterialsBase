<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialTag extends Model
{
    protected $fillable = [
        'id_material',
        'id_tag'
    ];

    protected $table = 'material_tag';
}