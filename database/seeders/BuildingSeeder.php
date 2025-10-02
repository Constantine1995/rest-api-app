<?php
namespace Database\Seeders;

use App\Models\Building;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Создание зданий...');

        Building::factory(20)->create();

        $testBuilding = Building::create([
            'address' => 'г. Москва, ул. Тестовая, д. 1',
            'latitude' => 55.6645,
            'longitude' => 37.6168,
        ]);

        Organization::create([
            'name' => 'Тестовая организация',
            'building_id' => $testBuilding->id,
        ]);

        $totalBuildings = Building::count();
        $this->command->info("Создано {$totalBuildings} зданий!");
    }
}
