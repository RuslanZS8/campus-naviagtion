<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'category_id' => 'nullable|exists:categories,id',
            'accessible' => 'boolean',
            'is_visible' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            'matterport_url' => 'nullable|url',
            'address' => 'nullable|string|max:500',
            'opening_hours' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Location name is required.',
            'lat.required' => 'Latitude is required.',
            'lng.required' => 'Longitude is required.',
            'lat.between' => 'Latitude must be between -90 and 90.',
            'lng.between' => 'Longitude must be between -180 and 180.',
        ];
    }
}
