<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertySearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'has_photo' => ['nullable', 'boolean'],

            'rooms' => ['nullable', 'array'],
            'rooms.*' => ['integer', 'min:1', 'max:10'],

            'area_from' => ['nullable', 'numeric', 'min:0'],
            'area_to' => ['nullable', 'numeric', 'min:0'],
            'sort' => [
                'nullable',
                Rule::in([
                    'default',
                    'area_asc',
                    'area_desc',
                    'rooms_asc',
                    'rooms_desc',
                    'floor_asc',
                    'floor_desc',
                    'created_desc',
                ]),
            ],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:48'],
        ];
    }
}
