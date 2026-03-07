<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
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
        'expires_at' => 'datetime'
    ];

    public function client(){ return $this->belongsTo(Client::class); }
    public function project(){ return $this->belongsTo(Project::class); }
    public function creator(){ return $this->belongsTo(User::class, 'created_by'); }
}