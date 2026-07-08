<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_visible' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:50',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->slug === null && $this->name) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }
}
