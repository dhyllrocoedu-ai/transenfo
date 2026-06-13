<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'violation_type_id' => ['required', 'exists:violation_types,id'],
            'driver_id' => ['nullable', 'exists:drivers,id'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'location' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
            'evidence' => ['nullable', 'array'],
            'evidence.*' => ['file', 'image', 'max:5120'],
        ];
    }
}
