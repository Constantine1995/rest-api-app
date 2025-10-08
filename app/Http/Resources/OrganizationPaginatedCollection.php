<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;

class OrganizationPaginatedCollection extends ResourceCollection
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'data' => OrganizationResource::collection($this->collection),
            'meta' => [
                'current_page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'last_page' => $this->lastPage(),
            ],
        ];
    }

    /**
     * @return JsonResponse
     */
    public function toJsonResponse(): JsonResponse
    {
        return response()->json($this->toArray(request()));
    }
}
