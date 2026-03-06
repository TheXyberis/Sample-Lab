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
}