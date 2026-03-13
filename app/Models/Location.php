<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['parent_id', 'name', 'path'];

    public function parent() {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function samples() {
        return $this->hasMany(Sample::class);
    }
}
