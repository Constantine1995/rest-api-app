<?php

namespace App\Http\Requests;

class IndexByRadiusRequest extends BaseApiRequest
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
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
            'radius' => 'numeric|min:0|max:1000',
        ]);
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'lat.required' => 'Параметр lat обязателен.',
            'lat.numeric' => 'Параметр lat должен быть числом.',
            'lat.between' => 'Параметр lat должен быть между -90 и 90.',
            'lon.required' => 'Параметр lon обязателен.',
            'lon.numeric' => 'Параметр lon должен быть числом.',
            'lon.between' => 'Параметр lon должен быть между -180 и 180.',
            'radius.numeric' => 'Параметр radius должен быть числом.',
            'radius.min' => 'Параметр radius должен быть не менее 0.',
            'radius.max' => 'Параметр radius не должен превышать 1000.',
        ]);
    }
}
