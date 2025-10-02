<?php

namespace App\Http\Requests;

class SearchByNameRequest extends BaseApiRequest
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
        return array_merge($this->commonRules(), [
            'name' => 'sometimes|string|max:255',
        ]);
    }

    /**
     * @return array|string[]
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'name.string' => 'Параметр name должен быть строкой.',
            'name.max' => 'Параметр name не должен превышать 255 символов.',
        ]);
    }
}
