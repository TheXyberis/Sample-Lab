<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ResultSet extends Model
{
    protected $fillable = [
        'measurement_id','status','submitted_by','submitted_at',
        'reviewed_by','reviewed_at','approved_by','approved_at','locked_at'
    ];

    public function results() {
        return $this->hasMany(Result::class);
    }

    public function measurement() {
        return $this->belongsTo(Measurement::class);
    }
}