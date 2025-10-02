<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Organization",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Тестовая организация"),
        new OA\Property(
            property: "building",
            ref: "#/components/schemas/Building"
        ),
        new OA\Property(
            property: "phones",
            type: "array",
            items: new OA\Items(type: "string", example: "+7-900-871-25-72")
        ),
        new OA\Property(
            property: "activities",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/Activity")
        ),
        new OA\Property(property: "distance", type: "number", format: "float", example: 4.799640864090197, nullable: true),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-10-01T13:07:30.000000Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-10-01T13:07:30.000000Z")
    ],
    type: "object"
)]
class OrganizationResource extends JsonResource
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
            'name' => $this->name,
            'building' => new BuildingResource($this->whenLoaded('building')),
            'phones' => $this->whenLoaded('phones')
                ? $this->phones->pluck('phone_number')
                : [],
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'distance' => $this->when(isset($this->distance), $this->distance),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
