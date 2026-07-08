<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location_id' => 'required|exists:locations,id',
            'image' => 'required|image|max:5120', // Max 5MB
            'caption' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'location_id.required' => 'Location ID is required.',
            'image.required' => 'Image file is required.',
            'image.image' => 'File must be an image.',
            'image.max' => 'Image size must not exceed 5MB.',
        ];
    }
}
