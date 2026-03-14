<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportLog extends Model
{
    protected $fillable = [
        'report_type',
        'entity_id',
        'filename',
        'file_path',
        'generated_by',
        'generated_at'
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'entity_id')->where('report_type', 'sample');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'entity_id')->where('report_type', 'project');
    }

    public function getEntityNameAttribute()
    {
        if ($this->report_type === 'sample') {
            $sample = Sample::find($this->entity_id);
            return $sample?->sample_code ?? 'Unknown Sample';
        } elseif ($this->report_type === 'project') {
            $project = Project::find($this->entity_id);
            return $project?->name ?? 'Unknown Project';
        }
        return 'Unknown';
    }
}
