<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Building",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 21),
        new OA\Property(property: "address", type: "string", example: "г. Москва, ул. Тестовая, д. 1"),
        new OA\Property(
            property: "coordinates",
            properties: [
                new OA\Property(property: "latitude", type: "number", format: "float", example: 55.6645),
                new OA\Property(property: "longitude", type: "number", format: "float", example: 37.6168)
            ],
            type: "object"
        ),
        new OA\Property(property: "organizations_count", type: "integer", example: 14),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-10-01T13:07:29.000000Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-10-01T13:07:29.000000Z")
    ],
    type: "object"
)]
class BuildingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'coordinates' => [
                'latitude' => (float)$this->latitude,
                'longitude' => (float)$this->longitude,
            ],
            'organizations_count' => $this->whenLoaded('organizations', $this->organizations->count()),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
