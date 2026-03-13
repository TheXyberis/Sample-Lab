<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'client_id',
        'code',
        'status'
    ];

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function samples() {
        return $this->hasMany(Sample::class);
    }
}