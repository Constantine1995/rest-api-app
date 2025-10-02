<?php

namespace App\Http\Requests;

class IndexByRectangleRequest extends BaseApiRequest
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
            'min_lat' => 'required|numeric|between:-90,90',
            'max_lat' => 'required|numeric|between:-90,90|gte:min_lat',
            'min_lon' => 'required|numeric|between:-180,180',
            'max_lon' => 'required|numeric|between:-180,180|gte:min_lon',
        ]);
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'min_lat.required' => 'Параметр min_lat обязателен.',
            'min_lat.numeric' => 'Параметр min_lat должен быть числом.',
            'min_lat.between' => 'Параметр min_lat должен быть между -90 и 90.',
            'max_lat.required' => 'Параметр max_lat обязателен.',
            'max_lat.numeric' => 'Параметр max_lat должен быть числом.',
            'max_lat.between' => 'Параметр max_lat должен быть между -90 и 90.',
            'max_lat.gte' => 'Параметр max_lat должен быть больше или равен min_lat.',
            'min_lon.required' => 'Параметр min_lon обязателен.',
            'min_lon.numeric' => 'Параметр min_lon должен быть числом.',
            'min_lon.between' => 'Параметр min_lon должен быть между -180 и 180.',
            'max_lon.required' => 'Параметр max_lon обязателен.',
            'max_lon.numeric' => 'Параметр max_lon должен быть числом.',
            'max_lon.between' => 'Параметр max_lon должен быть между -180 и 180.',
            'max_lon.gte' => 'Параметр max_lon должен быть больше или равен min_lon.',
        ]);
    }
}
