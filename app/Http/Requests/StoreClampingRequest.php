<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClampingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'location' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
            'evidence' => ['nullable', 'file', 'image', 'max:5120'],
        ];
    }
}
