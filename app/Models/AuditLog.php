<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'old_values',
        'new_values',
        'diff_json',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'diff_json' => 'json',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)->where('entity_id', $entityId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Static methods for creating audit logs
    public static function log($entityType, $entityId, $action, $oldValues = null, $newValues = null, $userId = null)
    {
        $diff = null;
        
        if ($oldValues && $newValues) {
            $diff = self::calculateDiff($oldValues, $newValues);
        }

        return self::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'diff_json' => $diff,
            'user_id' => $userId ?? auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    private static function calculateDiff($old, $new)
    {
        $diff = [];
        
        // Handle arrays/objects
        if (is_array($old) || is_object($old)) {
            $oldArray = (array) $old;
            $newArray = (array) $new;
            
            // Find added keys
            foreach ($newArray as $key => $value) {
                if (!array_key_exists($key, $oldArray)) {
                    $diff[$key] = [
                        'type' => 'added',
                        'new' => $value,
                    ];
                } elseif ($oldArray[$key] !== $value) {
                    $diff[$key] = [
                        'type' => 'changed',
                        'old' => $oldArray[$key],
                        'new' => $value,
                    ];
                }
            }
            
            // Find removed keys
            foreach ($oldArray as $key => $value) {
                if (!array_key_exists($key, $newArray)) {
                    $diff[$key] = [
                        'type' => 'removed',
                        'old' => $value,
                    ];
                }
            }
        } else {
            // Handle simple values
            if ($old !== $new) {
                $diff = [
                    'type' => 'changed',
                    'old' => $old,
                    'new' => $new,
                ];
            }
        }
        
        return $diff;
    }

    // Accessors
    public function getFormattedDiffAttribute()
    {
        if (!$this->diff_json) {
            return null;
        }

        $formatted = [];
        foreach ($this->diff_json as $key => $change) {
            switch ($change['type']) {
                case 'added':
                    $formatted[] = "{$key}: <span class='text-success'>+ {$change['new']}</span>";
                    break;
                case 'removed':
                    $formatted[] = "{$key}: <span class='text-danger'>- {$change['old']}</span>";
                    break;
                case 'changed':
                    $formatted[] = "{$key}: <span class='text-danger'>{$change['old']}</span> → <span class='text-success'>{$change['new']}</span>";
                    break;
            }
        }

        return implode('<br>', $formatted);
    }
}
