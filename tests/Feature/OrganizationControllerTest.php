<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Building;
use App\Models\Activity;
use App\Models\Organization;
use App\Models\OrganizationPhone;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->building = Building::factory()->create([
            'address' => 'г. Санкт-Петербург, ул. Советская, д. 44',
            'latitude' => 59.9311,
            'longitude' => 30.3609,
        ]);
        $this->otherBuilding = Building::factory()->create([
            'address' => 'г. Новосибирск, ул. Пушкина, д. 102, оф. 31',
            'latitude' => 55.1084,
            'longitude' => 82.9452,
        ]);

        $this->food = Activity::factory()->create(['level' => 1, 'name' => 'Еда', 'parent_id' => null]);
        $meat = Activity::factory()->create(['level' => 2, 'parent_id' => $this->food->id, 'name' => 'Мясная продукция']);
        $dairy = Activity::factory()->create(['level' => 2, 'parent_id' => $this->food->id, 'name' => 'Молочная продукция']);

        $this->org1 = Organization::factory()->create([
            'building_id' => $this->building->id,
            'name' => 'ООО "Деликатесы"',
        ]);
        $this->org2 = Organization::factory()->create([
            'building_id' => $this->otherBuilding->id,
            'name' => 'АО "Мясной Двор"',
        ]);

        $this->org1->activities()->attach([$this->food->id, $meat->id]);
        $this->org2->activities()->attach([$dairy->id]);

        OrganizationPhone::factory()->create([
            'organization_id' => $this->org1->id,
            'phone_number' => '+70000000001',
        ]);
    }

    /**
     * Проверяет получение организаций в конкретном здании.
     */
    public function test_returns_organizations_in_building()
    {
        $response = $this->json('GET', "/api/organizations/building/{$this->building->id}", ['per_page' => 10], ['X-API-KEY' => env('API_KEY')]);

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'ООО "Деликатесы"']);
    }

    /**
     * Проверяет обработку случая, когда в здании нет организаций.
     */
    public function test_no_organizations_in_building()
    {
        $emptyBuilding = Building::factory()->create();
        $response = $this->json('GET', "/api/organizations/building/{$emptyBuilding->id}",
            ['per_page' => 10],
            ['X-API-KEY' => env('API_KEY')]
        );
        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 0)
            ->assertJsonCount(0, 'data');
    }

    /**
     * Проверяет получение организаций по конкретному виду деятельности.
     */
    public function test_returns_organizations_by_activity()
    {
        $response = $this->json(
            'GET',
            "/api/organizations/activity/{$this->food->id}",
            ['per_page' => 10],
            ['X-API-KEY' => env('API_KEY')]
        );

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonFragment(['name' => 'ООО "Деликатесы"'])
            ->assertJsonMissing(['name' => 'АО "Мясной Двор"']);
    }

    /**
     * Проверяет обработку случая, когда для вида деятельности нет организаций.
     */
    public function test_no_organizations_for_activity()
    {
        $emptyActivity = Activity::factory()->create(['level' => 1, 'name' => 'IT', 'parent_id' => null]);
        $response = $this->json('GET', "/api/organizations/activity/{$emptyActivity->id}",
            ['per_page' => 10],
            ['X-API-KEY' => env('API_KEY')]
        );
        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 0)
            ->assertJsonCount(0, 'data');
    }

    /**
     * Проверяет геопоиск организаций в прямоугольной области.
     */
    public function test_returns_organizations_in_rectangle()
    {
        $this->building->update(['latitude' => 10, 'longitude' => 10]);
        $this->otherBuilding->update(['latitude' => 100, 'longitude' => 100]);

        $response = $this->json('GET', '/api/organizations/geo/rectangle', [
            'min_lat' => 0, 'max_lat' => 20,
            'min_lon' => 0, 'max_lon' => 20,
            'per_page' => 10
        ], ['X-API-KEY' => env('API_KEY')]);

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonFragment(['name' => 'ООО "Деликатесы"']);
    }

    /**
     * Проверяет валидацию параметров для прямоугольного геопоиска.
     */
    public function test_geo_rectangle_with_invalid_parameters()
    {
        $apiKey = env('API_KEY');
        if (empty($apiKey)) {
            $this->fail('API_KEY не установлен в .env');
        }

        // min_lat > max_lat
        $response = $this->json('GET', '/api/organizations/geo/rectangle', [
            'min_lat' => 20,
            'max_lat' => 10,
            'min_lon' => 0,
            'max_lon' => 20,
            'per_page' => 10
        ], ['X-API-KEY' => $apiKey]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Validation failed',
                'messages' => [
                    'max_lat' => ['Параметр max_lat должен быть больше или равен min_lat.'],
                ],
            ]);

        // некорректная долгота
        $response = $this->json('GET', '/api/organizations/geo/rectangle', [
            'min_lat' => 0,
            'max_lat' => 20,
            'min_lon' => -181,
            'max_lon' => 20,
            'per_page' => 10
        ], ['X-API-KEY' => $apiKey]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Validation failed',
                'messages' => [
                    'min_lon' => ['Параметр min_lon должен быть между -180 и 180.'],
                ],
            ]);

        // отсутствует параметр
        $response = $this->json('GET', '/api/organizations/geo/rectangle', [
            'min_lat' => 0,
            'max_lat' => 20,
            'max_lon' => 20,
            'per_page' => 10
        ], ['X-API-KEY' => $apiKey]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Validation failed',
                'messages' => [
                    'min_lon' => ['Параметр min_lon обязателен.'],
                ],
            ]);
    }

    /**
     * Проверяет корректность расчета расстояния по формуле Haversine.
     */
    public function test_haversine_formula_calculates_correct_distance()
    {
        $this->building->update(['latitude' => 0, 'longitude' => 0]);
        $this->otherBuilding->update(['latitude' => 1, 'longitude' => 1]);

        $response = $this->json('GET', '/api/organizations/geo/radius', [
            'lat' => 0, 'lon' => 0,
            'radius' => 200, 'per_page' => 10
        ], ['X-API-KEY' => env('API_KEY')]);

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('data.0.name', 'ООО "Деликатесы"')
            ->assertJsonPath('data.1.name', 'АО "Мясной Двор"');

        $expectedDistance = 157.25;
        $actualDistance = $response->json('data.1.distance');
        $this->assertTrue(
            abs($actualDistance - $expectedDistance) < 0.1,
            "Ожидаемое расстояние ~{$expectedDistance} км, получено {$actualDistance} км"
        );

        $this->assertEquals(0, $response->json('data.0.distance'), 'Расстояние для ООО "Деликатесы" должно быть 0 км');
    }

    /**
     * Проверяет валидацию параметров для геопоиска по радиусу.
     */
    public function test_geo_radius_with_invalid_parameters()
    {
        $apiKey = env('API_KEY');
        if (empty($apiKey)) {
            $this->fail('API_KEY не установлен в .env');
        }

        // отрицательный радиус
        $response = $this->json('GET', '/api/organizations/geo/radius', [
            'lat' => 0,
            'lon' => 0,
            'radius' => -10,
            'per_page' => 10
        ], ['X-API-KEY' => $apiKey]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Validation failed',
                'messages' => [
                    'radius' => ['Параметр radius должен быть не менее 0.'],
                ],
            ]);

        // некорректная широта
        $response = $this->json('GET', '/api/organizations/geo/radius', [
            'lat' => 91,
            'lon' => 0,
            'radius' => 200,
            'per_page' => 10
        ], ['X-API-KEY' => $apiKey]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Validation failed',
                'messages' => [
                    'lat' => ['Параметр lat должен быть между -90 и 90.'],
                ],
            ]);

        // отсутствует параметр
        $response = $this->json('GET', '/api/organizations/geo/radius', [
            'lon' => 0,
            'radius' => 200,
            'per_page' => 10
        ], ['X-API-KEY' => $apiKey]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Validation failed',
                'messages' => [
                    'lat' => ['Параметр lat обязателен.'],
                ],
            ]);
    }

    /**
     * Проверяет геопоиск в малом радиусе.
     */
    public function test_haversine_formula_with_small_radius()
    {
        $this->building->update(['latitude' => 0, 'longitude' => 0]);
        $this->otherBuilding->update(['latitude' => 1, 'longitude' => 1]);

        $response = $this->json('GET', '/api/organizations/geo/radius', [
            'lat' => 0, 'lon' => 0,
            'radius' => 10, 'per_page' => 10
        ], ['X-API-KEY' => env('API_KEY')]);

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'ООО "Деликатесы"')
            ->assertJsonMissing(['name' => 'АО "Мясной Двор"']);
    }

    /**
     * Проверяет получение полной информации об организации по ID.
     */
    public function test_returns_organization_by_id()
    {
        $response = $this->json('GET', "/api/organizations/{$this->org1->id}", [], ['X-API-KEY' => env('API_KEY')]);

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $this->org1->id, 'name' => 'ООО "Деликатесы"']);
    }

    /**
     * Проверяет поиск организаций по названию.
     */
    public function test_searches_organizations_by_name()
    {
        Organization::factory()->create([
            'building_id' => $this->building->id,
            'name' => 'Delicatessen LLC'
        ]);

        $response = $this->json('GET', '/api/organizations/search',
            ['name' => 'Delicatessen', 'per_page' => 10],
            ['X-API-KEY' => env('API_KEY')]
        );

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonFragment(['name' => 'Delicatessen LLC']);
    }

    /**
     * Проверяет поиск организаций по виду деятельности включая дочерние категории.
     */
    public function test_returns_organizations_by_activity_with_children()
    {
        $response = $this->json(
            'GET',
            "/api/organizations/activity-with-children/{$this->food->id}",
            ['per_page' => 10],
            ['X-API-KEY' => env('API_KEY')]
        );

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 2)
            ->assertJsonFragment(['name' => 'ООО "Деликатесы"'])
            ->assertJsonFragment(['name' => 'АО "Мясной Двор"']);
    }

    /**
     * Проверяет обработку случая, когда для вида деятельности и его потомков нет организаций.
     */
    public function test_no_organizations_for_activity_with_children()
    {
        $emptyActivity = Activity::factory()->create(['level' => 1, 'name' => 'IT', 'parent_id' => null]);
        $response = $this->json('GET', "/api/organizations/activity-with-children/{$emptyActivity->id}",
            ['per_page' => 10],
            ['X-API-KEY' => env('API_KEY')]
        );
        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 0)
            ->assertJsonCount(0, 'data');
    }

    /**
     * Проверяет возможность создания деятельности третьего уровня вложенности.
     */
    public function test_allows_activity_level_three()
    {
        $level1 = Activity::create(['name' => 'Level 1', 'level' => 1, 'parent_id' => null]);
        $level2 = Activity::create(['name' => 'Level 2', 'level' => 2, 'parent_id' => $level1->id]);
        $level3 = Activity::create(['name' => 'Level 3', 'level' => 3, 'parent_id' => $level2->id]);

        $this->assertDatabaseHas('activities', [
            'name' => 'Level 3',
            'level' => 3,
            'parent_id' => $level2->id,
        ]);

        $this->assertEquals($level2->id, $level3->parent_id);

        $this->assertEquals(3, $level3->level);
        $this->assertEquals('Level 3', $level3->name);
    }

    /**
     * Проверяет ограничение вложенности видов деятельности тремя уровнями.
     */
    public function test_limits_activity_level_to_three()
    {
        Activity::clearBootedModels();

        $level1 = Activity::create(['name' => 'Level 1', 'level' => 1, 'parent_id' => null]);
        $level2 = Activity::create(['name' => 'Level 2', 'level' => 2, 'parent_id' => $level1->id]);
        $level3 = Activity::create(['name' => 'Level 3', 'level' => 3, 'parent_id' => $level2->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Максимальная вложенность — 3 уровня');

        Activity::create(['name' => 'Level 4', 'parent_id' => $level3->id]);
    }

    /**
     * Проверяет пагинацию.
     */
    public function test_pagination()
    {
        Organization::factory()->count(20)->create(['building_id' => $this->building->id]);
        $response = $this->json('GET', "/api/organizations/building/{$this->building->id}", ['per_page' => 5], ['X-API-KEY' => env('API_KEY')]);
        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 21)
            ->assertJsonPath('meta.last_page', 5);
    }

    /**
     * Проверяет обработку запроса без API-ключа.
     */
    public function test_unauthorized_without_api_key()
    {
        $response = $this->json('GET', "/api/organizations/building/{$this->building->id}");

        $response->assertStatus(401)
            ->assertJsonFragment(['error' => 'Unauthorized: Invalid or missing API key']);
    }

    /**
     * Проверяет обработку запроса с неверным API-ключом.
     */
    public function test_invalid_api_key()
    {
        $response = $this->json('GET', "/api/organizations/building/{$this->building->id}", [], ['X-API-KEY' => 'invalid_key']);
        $response->assertStatus(401)->assertJsonFragment(['error' => 'Unauthorized: Invalid or missing API key']);
    }
}
