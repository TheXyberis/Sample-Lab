<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResultStoreRequest extends FormRequest
{
    public function authorize()
    {
        $user = auth()->user();
        
        if ($this->route()->getName() === 'results.saveDraft') {
            return $user->hasRole(['Admin', 'Laborant']);
        }
        
        if ($this->route()->getName() === 'results.submit') {
            return $user->hasRole(['Admin', 'Laborant']);
        }
        
        if ($this->route()->getName() === 'results.review') {
            return $user->hasRole(['Admin', 'QC/Reviewer']);
        }
        
        if ($this->route()->getName() === 'results.approve') {
            return $user->hasRole(['Admin', 'QC/Reviewer']);
        }
        
        if ($this->route()->getName() === 'results.lock') {
            return $user->hasRole(['Admin', 'QC/Reviewer']);
        }
        
        if ($this->route()->getName() === 'results.unlock') {
            return $user->hasRole(['Admin', 'QC/Reviewer']);
        }
        
        return false;
    }

    public function rules()
    {
        $measurementId = $this->route('id');
        $measurement = \App\Models\Measurement::with('method')->findOrFail($measurementId);
        
        $schema = is_array($measurement->method->schema_json) 
            ? $measurement->method->schema_json 
            : json_decode($measurement->method->schema_json, true);
        
        $fields = $schema['fields'] ?? [];
        $rules = ['results' => 'required|array'];
        
        foreach ($fields as $field) {
            $key = $field['key'] ?? null;
            if (!$key) continue;
            
            $fieldRules = [];
            
            if (!empty($field['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            
            switch ($field['type'] ?? 'text') {
                case 'number':
                    $fieldRules[] = 'numeric';
                    if (isset($field['min'])) {
                        $fieldRules[] = 'min:' . $field['min'];
                    }
                    if (isset($field['max'])) {
                        $fieldRules[] = 'max:' . $field['max'];
                    }
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'select':
                    $options = $field['options'] ?? [];
                    if (!empty($options)) {
                        $fieldRules[] = 'in:' . implode(',', $options);
                    }
                    break;
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'text':
                default:
                    $fieldRules[] = 'string';
                    if (isset($field['max_length'])) {
                        $fieldRules[] = 'max:' . $field['max_length'];
                    } else {
                        $fieldRules[] = 'max:255';
                    }
                    break;
            }
            
            $rules["results.{$key}"] = $fieldRules;
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
            'results.required' => 'At least one result field must be provided.',
            'results.array' => 'Results must be provided as an array.',
        ];
    }
}
