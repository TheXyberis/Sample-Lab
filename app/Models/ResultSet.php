<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ResultSet extends Model
{
    protected $fillable = [
        'measurement_id','status','submitted_by','submitted_at',
        'reviewed_by','reviewed_at','approved_by','approved_at','locked_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function results() {
        return $this->hasMany(Result::class);
    }

    public function measurement() {
        return $this->belongsTo(Measurement::class);
    }

    public function submitter() {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approved_by');
    }
}