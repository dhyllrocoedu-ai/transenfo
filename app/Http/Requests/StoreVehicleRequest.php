<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'owner_id' => ['nullable', 'exists:users,id'],
            'driver_id' => ['nullable', 'exists:drivers,id'],
            'plate_number' => ['required', 'string', 'max:20', 'unique:vehicles,plate_number'],
            'classification' => ['required', 'string', 'max:100'],
            'make' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'registration_status' => ['required', 'in:active,suspended,expired'],
        ];
    }
}
