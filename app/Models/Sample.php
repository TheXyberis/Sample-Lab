<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sample extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sample_code',
        'client_id',
        'project_id',
        'type',
        'name',
        'status',
        'location_id',
        'quantity',
        'unit',
        'collected_at',
        'received_at',
        'expires_at',
        'metadata_json',
        'barcode_value',
        'qr_value',
        'created_by'
    ];

    protected $casts = [
        'metadata_json' => 'array',
        'collected_at' => 'datetime',
        'received_at' => 'datetime',
        'expires_at' => 'datetime',
        'quantity' => 'decimal:2'
    ];

    public function client() { return $this->belongsTo(Client::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function measurements() { return $this->hasMany(Measurement::class); }
    public function location() { return $this->belongsTo(Location::class, 'location_id'); }
    
    public static function generateSampleCode()
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'S-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
    
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'REGISTERED' => 'primary',
            'IN_PROGRESS' => 'warning',
            'COMPLETED' => 'success',
            'ARCHIVED' => 'secondary',
            'DISPOSED' => 'dark'
        ];
        return $colors[$this->status ?? ''] ?? 'secondary';
    }
}