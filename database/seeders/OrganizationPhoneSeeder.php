<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\OrganizationPhone;
use Illuminate\Database\Seeder;

class OrganizationPhoneSeeder extends Seeder
{

    /**
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Создание телефонов организаций...');

        $organizations = Organization::all();

        if ($organizations->isEmpty()) {
            $this->command->error('Организации не найдены');
            return;
        }

        $totalPhones = 0;

        foreach ($organizations as $organization) {
            $phoneCount = rand(1, 5);

            for ($i = 0; $i < $phoneCount; $i++) {
                switch ($i) {
                    case 0:
                        OrganizationPhone::factory()
                            ->mobile()
                            ->create(['organization_id' => $organization->id]);
                        break;
                    case 1:
                        OrganizationPhone::factory()
                            ->landline()
                            ->create(['organization_id' => $organization->id]);
                        break;
                    case 2:
                        OrganizationPhone::factory()
                            ->short()
                            ->create(['organization_id' => $organization->id]);
                        break;
                    default:
                        OrganizationPhone::factory()
                            ->create(['organization_id' => $organization->id]);
                        break;
                }
                $totalPhones++;
            }
        }

        $hornsHooves = Organization::where('name', 'LIKE', '%Рога и Копыта%')->first();
        if ($hornsHooves) {
            OrganizationPhone::firstOrCreate([
                'organization_id' => $hornsHooves->id,
                'phone_number' => '2-222-222'
            ]);

            OrganizationPhone::firstOrCreate([
                'organization_id' => $hornsHooves->id,
                'phone_number' => '3-333-333'
            ]);

            OrganizationPhone::firstOrCreate([
                'organization_id' => $hornsHooves->id,
                'phone_number' => '8-923-666-13-13'
            ]);

            $totalPhones += 3;
        }

        $this->command->info("Создано {$totalPhones} телефонов!");
    }
}
