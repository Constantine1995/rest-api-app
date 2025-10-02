<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{

    /**
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Создание организаций...');

        $buildings = Building::all();

        if ($buildings->isEmpty()) {
            $this->command->error('Здания не найдены!');
            return;
        }

        Organization::factory(60)
            ->food()
            ->create([
                'building_id' => fn() => $buildings->random()->id
            ]);

        Organization::factory(50)
            ->tech()
            ->create([
                'building_id' => fn() => $buildings->random()->id
            ]);

        Organization::factory(40)
            ->automotive()
            ->create([
                'building_id' => fn() => $buildings->random()->id
            ]);

        Organization::factory(50)
            ->create([
                'building_id' => fn() => $buildings->random()->id
            ]);

        $moscowBuilding = Building::where('address', 'LIKE', '%Москва, ул. Ленина%')->first();
        if ($moscowBuilding) {
            Organization::create([
                'name' => 'ООО "Рога и Копыта"',
                'building_id' => $moscowBuilding->id,
            ]);

            Organization::create([
                'name' => 'ИП Иванов (Молочная Река)',
                'building_id' => $moscowBuilding->id,
            ]);
        }

        // Распределяние организации по зданиям
        $remainingBuildings = $buildings->skip(Organization::count());
        foreach ($remainingBuildings as $building) {
            if (rand(1, 100) <= 70) {
                Organization::factory(rand(1, 3))->create([
                    'building_id' => $building->id
                ]);
            }
        }

        $totalOrganizations = Organization::count();
        $this->command->info("Создано {$totalOrganizations} организаций!");

    }
}
