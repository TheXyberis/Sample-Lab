<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Method extends Model
{
    protected $fillable = [
        'base_method_id',
        'name',
        'version',
        'status',
        'schema_json',
        'limits_json',
        'created_by'
    ];

    protected $casts = [
        'schema_json' => 'array',
        'limits_json' => 'array'
    ];
}
