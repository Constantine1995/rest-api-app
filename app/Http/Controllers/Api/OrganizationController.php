<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexByActivityRequest;
use App\Http\Requests\IndexByBuildingRequest;
use App\Http\Requests\IndexByRadiusRequest;
use App\Http\Requests\IndexByRectangleRequest;
use App\Http\Requests\SearchByActivityRequest;
use App\Http\Requests\SearchByNameRequest;
use App\Http\Resources\OrganizationPaginatedCollection;
use App\Http\Resources\OrganizationResource;
use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "API для работы с организациями, их видами деятельности и географическими данными",
    title: "Organizations API"
)]
#[OA\Server(
    url: "http://localhost:85/api",
    description: "Локальный сервер"
)]
#[OA\SecurityScheme(
    securityScheme: "ApiKeyAuth",
    type: "apiKey",
    description: "API ключ для авторизации (например, 5fff06a3-3c1f-4d22-a06f-5e140ec64b70)",
    name: "X-API-KEY",
    in: "header"
)]
#[OA\Tag(
    name: "Organizations",
    description: "Операции с организациями"
)]
#[OA\Schema(
    schema: "PaginatedResponse",
    properties: [
        new OA\Property(
            property: "data",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/Organization")
        ),
        new OA\Property(
            property: "meta",
            properties: [
                new OA\Property(property: "current_page", type: "integer", example: 1),
                new OA\Property(property: "per_page", type: "integer", example: 15),
                new OA\Property(property: "total", type: "integer", example: 50),
                new OA\Property(property: "last_page", type: "integer", example: 4)
            ],
            type: "object"
        )
    ],
    type: "object"
)]
class OrganizationController extends Controller
{
    #[OA\Get(
        path: "/organizations/building/{building}",
        summary: "Список организаций в конкретном здании",
        security: [["ApiKeyAuth" => []]],
        tags: ["Organizations"],
        parameters: [
            new OA\Parameter(
                name: "building",
                description: "ID здания",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "per_page",
                description: "Количество записей на страницу",
                in: "query",
                schema: new OA\Schema(type: "integer", default: 15, maximum: 500, minimum: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(ref: "#/components/schemas/PaginatedResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизован",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Unauthorized: Invalid or missing API key")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function indexByBuilding(IndexByBuildingRequest $request, Building $building): JsonResponse
    {
        $perPage = $request->get('per_page', 15);

        $organizations = Organization::with(['phones', 'activities'])
            ->where('building_id', $building->id)
            ->orderBy('name')
            ->paginate($perPage);

        return (new OrganizationPaginatedCollection($organizations))->toJsonResponse();
    }

    #[OA\Get(
        path: "/organizations/activity/{activity}",
        summary: "Список организаций по виду деятельности",
        security: [["ApiKeyAuth" => []]],
        tags: ["Organizations"],
        parameters: [
            new OA\Parameter(
                name: "activity",
                description: "ID вида деятельности",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "per_page",
                description: "Количество записей на страницу",
                in: "query",
                schema: new OA\Schema(type: "integer", default: 15, maximum: 500, minimum: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(ref: "#/components/schemas/PaginatedResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизован",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Unauthorized: Invalid or missing API key")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function indexByActivity(IndexByActivityRequest $request, Activity $activity): JsonResponse
    {
        $perPage = $request->get('per_page', 15);

        $organizations = Organization::with(['phones', 'activities'])
            ->whereHas('activities', fn($q) => $q->where('activities.id', $activity->id))
            ->orderBy('name')
            ->paginate($perPage);

        return (new OrganizationPaginatedCollection($organizations))->toJsonResponse();
    }

    #[OA\Get(
        path: "/organizations/geo/rectangle",
        summary: "Список организаций в заданной прямоугольной области",
        security: [["ApiKeyAuth" => []]],
        tags: ["Organizations"],
        parameters: [
            new OA\Parameter(
                name: "min_lat",
                description: "Минимальная широта",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "number", format: "float", maximum: 90, minimum: -90)
            ),
            new OA\Parameter(
                name: "max_lat",
                description: "Максимальная широта",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "number", format: "float", maximum: 90, minimum: -90)
            ),
            new OA\Parameter(
                name: "min_lon",
                description: "Минимальная долгота",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "number", format: "float", maximum: 180, minimum: -180)
            ),
            new OA\Parameter(
                name: "max_lon",
                description: "Максимальная долгота",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "number", format: "float", maximum: 180, minimum: -180)
            ),
            new OA\Parameter(
                name: "per_page",
                description: "Количество записей на страницу",
                in: "query",
                schema: new OA\Schema(type: "integer", default: 100, maximum: 500, minimum: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(ref: "#/components/schemas/PaginatedResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизован",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Unauthorized: Invalid or missing API key")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function indexByRectangle(IndexByRectangleRequest $request): JsonResponse
    {
        $minLat = (float)$request->get('min_lat');
        $maxLat = (float)$request->get('max_lat');
        $minLon = (float)$request->get('min_lon');
        $maxLon = (float)$request->get('max_lon');
        $perPage = $request->get('per_page', 100);

        \Log::info("Прямоугольник: min_lat=$minLat, max_lat=$maxLat, min_lon=$minLon, max_lon=$maxLon");

        $organizations = Organization::with(['phones', 'activities', 'building'])
            ->whereHas('building', function ($query) use ($minLat, $maxLat, $minLon, $maxLon) {
                $query->whereBetween('latitude', [$minLat, $maxLat])
                    ->whereBetween('longitude', [$minLon, $maxLon]);
            })
            ->orderBy('name')
            ->paginate($perPage);

        \Log::info("Найдено организаций: " . $organizations->count());

        return (new OrganizationPaginatedCollection($organizations))->toJsonResponse();
    }

    #[OA\Get(
        path: "/organizations/geo/radius",
        summary: "Список организаций в заданном радиусе",
        security: [["ApiKeyAuth" => []]],
        tags: ["Organizations"],
        parameters: [
            new OA\Parameter(
                name: "lat",
                description: "Широта центра области",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "number", format: "float", maximum: 90, minimum: -90)
            ),
            new OA\Parameter(
                name: "lon",
                description: "Долгота центра области",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "number", format: "float", minimum: -180, maximum: 180)
            ),
            new OA\Parameter(
                name: "radius",
                description: "Радиус в километрах",
                in: "query",
                schema: new OA\Schema(type: "number", format: "float", default: 15, maximum: 1000, minimum: 0)
            ),
            new OA\Parameter(
                name: "per_page",
                description: "Количество записей на страницу",
                in: "query",
                schema: new OA\Schema(type: "integer", default: 50, minimum: 1, maximum: 500)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(ref: "#/components/schemas/PaginatedResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизован",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Unauthorized: Invalid or missing API key")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function indexByRadius(IndexByRadiusRequest $request): JsonResponse
    {
        $lat = $request->get('lat');
        $lon = $request->get('lon');
        $radius = $request->get('radius', 15);
        $perPage = $request->get('per_page', 50);

        $haversine = "(6371 * acos(cos(radians($lat)) * cos(radians(buildings.latitude)) * cos(radians(buildings.longitude) - radians($lon)) + sin(radians($lat)) * sin(radians(buildings.latitude))))";

        $organizations = Organization::with(['phones', 'activities', 'building'])
            ->select('organizations.*')
            ->selectRaw("$haversine AS distance")
            ->join('buildings', 'organizations.building_id', '=', 'buildings.id')
            ->whereRaw("$haversine < ?", [$radius])
            ->orderBy('distance')
            ->paginate($perPage);

        return (new OrganizationPaginatedCollection($organizations))->toJsonResponse();
    }

    #[OA\Get(
        path: "/organizations/{id}",
        summary: "Информация об организации по ID",
        security: [["ApiKeyAuth" => []]],
        tags: ["Organizations"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID организации",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(ref: "#/components/schemas/Organization")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизован",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Unauthorized: Invalid or missing API key")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Организация не найдена",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "No query results for model [App\\Models\\Organization].")
                    ]
                )
            )
        ]
    )]
    public function searchById($id): JsonResponse
    {
        $id = (int)$id;
        $organization = Organization::with(['building', 'phones', 'activities'])->findOrFail($id);
        return response()->json(new OrganizationResource($organization));
    }

    #[OA\Get(
        path: "/organizations/search",
        summary: "Поиск организаций по названию",
        security: [["ApiKeyAuth" => []]],
        tags: ["Organizations"],
        parameters: [
            new OA\Parameter(
                name: "name",
                description: "Название организации для поиска (частичное совпадение)",
                in: "query",
                schema: new OA\Schema(type: "string", maxLength: 255)
            ),
            new OA\Parameter(
                name: "per_page",
                description: "Количество записей на страницу",
                in: "query",
                schema: new OA\Schema(type: "integer", default: 15, maximum: 500, minimum: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(ref: "#/components/schemas/PaginatedResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизован",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Unauthorized: Invalid or missing API key")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function searchByName(SearchByNameRequest $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $query = $request->get('name', '');

        $organizations = Organization::with(['phones', 'activities'])
            ->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($query) . '%']) // ILIKE не используется потому что тесты на SQLite
            ->orderBy('name')
            ->paginate($perPage);

        return (new OrganizationPaginatedCollection($organizations))->toJsonResponse();
    }

    #[OA\Get(
        path: "/organizations/activity-with-children/{activity}",
        summary: "Поиск организаций по виду деятельности и его дочерним категориям",
        security: [["ApiKeyAuth" => []]],
        tags: ["Organizations"],
        parameters: [
            new OA\Parameter(
                name: "activity",
                description: "ID вида деятельности",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "per_page",
                description: "Количество записей на страницу",
                in: "query",
                schema: new OA\Schema(type: "integer", default: 15, maximum: 500, minimum: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(ref: "#/components/schemas/PaginatedResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Неавторизован",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Unauthorized: Invalid or missing API key")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации",
                content: new OA\JsonContent(ref: "#/components/schemas/ErrorResponse")
            )
        ]
    )]
    public function searchByActivity(SearchByActivityRequest $request, Activity $activity): JsonResponse
    {
        $perPage = $request->get('per_page', 15);

        // Получаем ID активности + потомков
        $activityIds = Activity::getDescendantIds($activity->id);

        // Фильтрация организации по этим активностям
        $organizations = Organization::with(['phones', 'activities'])
            ->whereHas('activities', function ($q) use ($activityIds) {
                $q->whereIn('activities.id', $activityIds);
            })
            ->orderBy('name')
            ->paginate($perPage);

        return (new OrganizationPaginatedCollection($organizations))->toJsonResponse();
    }
}
