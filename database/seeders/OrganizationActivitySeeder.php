<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationActivitySeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Создание отношений «организация-деятельность»...');

        $organizations = Organization::all();
        $activities = Activity::all();

        if ($organizations->isEmpty() || $activities->isEmpty()) {
            $this->command->error('Организации или виды деятельности не найдены!');
            return;
        }

        DB::table('organization_activities')->truncate();

        $foodActivities = Activity::getDescendantIds(1);
        $autoActivities = Activity::getDescendantIds(2);
        $itActivities = Activity::getDescendantIds(4);
        $constructionActivities = Activity::getDescendantIds(3);
        $medicalActivities = Activity::getDescendantIds(5);

        $totalConnections = 0;

        foreach ($organizations as $organization) {
            $orgName = strtolower($organization->name);
            $possibleActivities = [];
            $activityCount = rand(1, 3);

            if (str_contains($orgName, 'рога') || str_contains($orgName, 'молочная') ||
                str_contains($orgName, 'мясной') || str_contains($orgName, 'хлебный') ||
                str_contains($orgName, 'деликатесы') || str_contains($orgName, 'фермер') ||
                str_contains($orgName, 'сладкий') || str_contains($orgName, 'золотой колос')) {
                $possibleActivities = $foodActivities;

            } elseif (str_contains($orgName, 'авто') || str_contains($orgName, 'машина') ||
                str_contains($orgName, 'колесо') || str_contains($orgName, 'мотор') ||
                str_contains($orgName, 'гараж') || str_contains($orgName, 'сто') ||
                str_contains($orgName, 'шиномонтаж')) {
                $possibleActivities = $autoActivities;

            } elseif (str_contains($orgName, 'цифровые') || str_contains($orgName, 'код') ||
                str_contains($orgName, 'веб') || str_contains($orgName, 'айти') ||
                str_contains($orgName, 'софт') || str_contains($orgName, 'технологии') ||
                str_contains($orgName, 'диджитал') || str_contains($orgName, 'программист') ||
                str_contains($orgName, 'инновации') || str_contains($orgName, 'системы')) {
                $possibleActivities = $itActivities;

            } elseif (str_contains($orgName, 'строй') || str_contains($orgName, 'дом строй') ||
                str_contains($orgName, 'кирпич') || str_contains($orgName, 'ремонт') ||
                str_contains($orgName, 'фундамент') || str_contains($orgName, 'новостройки') ||
                str_contains($orgName, 'отделка')) {
                $possibleActivities = $constructionActivities;

            } elseif (str_contains($orgName, 'медицина') || str_contains($orgName, 'здоровье') ||
                str_contains($orgName, 'стоматология') || str_contains($orgName, 'доктор') ||
                str_contains($orgName, 'клиника') || str_contains($orgName, 'мед центр') ||
                str_contains($orgName, 'поликлиника') || str_contains($orgName, 'лечебница')) {
                $possibleActivities = $medicalActivities;

            } else {
                $activityTypes = [
                    $foodActivities,
                    $autoActivities,
                    $itActivities,
                    $constructionActivities,
                    $medicalActivities
                ];
                $possibleActivities = collect($activityTypes)->random();
            }

            if (empty($possibleActivities)) {
                $possibleActivities = $foodActivities;
            }

            $selectedActivities = collect($possibleActivities)
                ->random(min($activityCount, count($possibleActivities)))
                ->toArray();

            foreach ($selectedActivities as $activityId) {
                try {
                    DB::table('organization_activities')->insert([
                        'organization_id' => $organization->id,
                        'activity_id' => $activityId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $totalConnections++;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        $this->command->info("Создано {$totalConnections} связей!");
    }
}
