<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PrintExamsRequest extends FormRequest
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
            '*.id' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($value !== 0) {
                        if (! DB::table('packages')->where('id', $value)->exists()) {
                            $fail("The selected $attribute is invalid.");
                        }
                    }
                },
            ],
            '*.name' => [
                'exclude_if:id,!=,0',
                'sometimes',
                'string',
                'max:255',
            ],
            '*.exams' => [
                'required_if:id,!=,0',
                'array',
            ],
            '*.exams.*.id' => [
                'required',
                'integer',
                'exists:exams,id',
            ],
            '*.exams.*.name' => [
                'required',
                'string',
                'max:255',
            ],
            '*.exams.*.laterality' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in(['AO', 'OD', 'OE']),
            ],
            '*.exams.*.group' => [
                'required',
                'string',
                Rule::in(['Individual', 'Grupo 1', 'Grupo 2', 'Grupo 3', 'Grupo 4', 'Grupo 5']),
            ],
            '*.exams.*.comment' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],
            '*.observations' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
