<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackageRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('packages', 'name'),
            ],
            'observations' => [
                'sometimes',
                'string',
                'max:1000',
            ],
            'exams' => [
                'required',
                'array',
                'min:1',
            ],
            'exams.*' => [
                'integer',
                Rule::exists('exams', 'id'),
            ],
        ];
    }
}
