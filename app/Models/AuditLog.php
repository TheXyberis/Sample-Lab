<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'entity_type', 'entity_id', 'diff_json', 'user_id', 'action'
    ];
}
