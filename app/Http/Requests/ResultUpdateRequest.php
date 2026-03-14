<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResultUpdateRequest extends FormRequest
{
    public function authorize()
    {
        $user = auth()->user();
        $measurement = $this->route('measurement');
        
        if (!$user->hasPermissionTo('results:edit')) {
            return false;
        }
        
        $currentResultSet = $measurement->resultSets->sortByDesc('created_at')->first();
        if ($currentResultSet && $currentResultSet->status === 'LOCKED') {
            return $user->hasPermissionTo('results:unlock');
        }
        
        return true;
    }

    public function rules()
    {
        $rules = [
            'results' => 'required|array',
            'results.*.field_key' => 'required|string',
            'results.*.value' => 'required',
        ];

        $measurement = $this->route('measurement');
        $schema = $measurement->method->schema_json['fields'] ?? [];

        foreach ($this->input('results', []) as $fieldKey => $value) {
            $fieldSchema = collect($schema)->firstWhere('key', $fieldKey);
            
            if ($fieldSchema) {
                $fieldRules = [];
                
                switch ($fieldSchema['type'] ?? 'text') {
                    case 'number':
                        $fieldRules[] = 'numeric';
                        if (isset($fieldSchema['min'])) {
                            $fieldRules[] = 'min:' . $fieldSchema['min'];
                        }
                        if (isset($fieldSchema['max'])) {
                            $fieldRules[] = 'max:' . $fieldSchema['max'];
                        }
                        break;
                    case 'date':
                        $fieldRules[] = 'date';
                        break;
                    case 'select':
                        $options = $fieldSchema['options'] ?? [];
                        $fieldRules[] = Rule::in($options);
                        break;
                    default:
                        $fieldRules[] = 'string';
                        if (isset($fieldSchema['max_length'])) {
                            $fieldRules[] = 'max:' . $fieldSchema['max_length'];
                        }
                }
                
                if ($fieldSchema['required'] ?? false) {
                    $fieldRules[] = 'required';
                }
                
                $rules["results.{$fieldKey}"] = $fieldRules;
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'results.required' => 'At least one result field is required.',
            'results.*.field_key.required' => 'Field key is required.',
            'results.*.value.required' => 'Field value is required.',
            'results.*.numeric' => 'This field must be a number.',
            'results.*.date' => 'This field must be a valid date.',
            'results.*.min' => 'Value is below the minimum allowed.',
            'results.*.max' => 'Value is above the maximum allowed.',
        ];
    }
}
