<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ErrorResponse",
    properties: [
        new OA\Property(property: "error", type: "string", example: "Validation failed"),
        new OA\Property(
            property: "messages",
            type: "object",
            example: [
                "per_page" => "Параметр per_page должен быть целым числом.",
                "lat" => "Параметр lat обязателен."
            ],
            additionalProperties: true
        )
    ],
    type: "object"
)]
abstract class BaseApiRequest extends FormRequest
{
    /**
     * @return string[]
     */
    protected function commonRules(): array
    {
        return [
            'per_page' => 'integer|min:1|max:500',
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'per_page.integer' => 'Параметр per_page должен быть целым числом.',
            'per_page.min' => 'Параметр per_page должен быть не менее 1.',
            'per_page.max' => 'Параметр per_page не должен превышать 1000.',
        ];
    }

    /**
     * @param Validator $validator
     * @return mixed
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 422)
        );
    }
}
