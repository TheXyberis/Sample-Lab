<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
            return false;
        }

        return $this->roles()->where('name', $roles)->exists();
    }

    public function getLegacyRoleAttribute()
    {
        return $this->roles->first()?->name ?? 'User';
    }

    public function createdSamples()
    {
        return $this->hasMany(Sample::class, 'created_by');
    }

    public function assignedMeasurements()
    {
        return $this->hasMany(Measurement::class, 'assignee_id');
    }

    public function submittedResultSets()
    {
        return $this->hasMany(ResultSet::class, 'submitted_by');
    }

    public function reviewedResultSets()
    {
        return $this->hasMany(ResultSet::class, 'reviewed_by');
    }

    public function approvedResultSets()
    {
        return $this->hasMany(ResultSet::class, 'approved_by');
    }

    public function rejectedResultSets()
    {
        return $this->hasMany(ResultSet::class, 'rejected_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }
}