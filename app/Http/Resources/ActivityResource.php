<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Activity",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 12),
        new OA\Property(property: "name", type: "string", example: "Аксессуары"),
        new OA\Property(property: "parent_id", type: "integer", example: 2, nullable: true),
        new OA\Property(property: "level", type: "integer", example: 2),
        new OA\Property(
            property: "children",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/Activity")
        ),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-10-01T13:07:29.000000Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-10-01T13:07:29.000000Z")
    ],
    type: "object"
)]
class ActivityResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'level' => $this->level,
            'children' => ActivityResource::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
