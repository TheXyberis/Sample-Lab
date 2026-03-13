<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SampleStoreRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole(['Admin', 'Manager', 'Laborant']);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'quantity' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'collected_at' => 'nullable|date|before_or_equal:today',
            'received_at' => 'nullable|date|after_or_equal:collected_at|before_or_equal:today',
            'expires_at' => 'nullable|date|after:received_at',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Sample name is required.',
            'type.required' => 'Sample type is required.',
            'client_id.exists' => 'Selected client does not exist.',
            'project_id.exists' => 'Selected project does not exist.',
            'quantity.numeric' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity cannot be negative.',
            'collected_at.before_or_equal' => 'Collection date cannot be in the future.',
            'received_at.after_or_equal' => 'Received date must be on or after collection date.',
            'received_at.before_or_equal' => 'Received date cannot be in the future.',
            'expires_at.after' => 'Expiry date must be after received date.',
        ];
    }
}
