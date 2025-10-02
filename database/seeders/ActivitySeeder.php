<?php

namespace Database\Seeders;

use App\Models\Activity;
use Database\Factories\ActivityFactory;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        Activity::truncate();

        $factory = ActivityFactory::new();
        $factory->createHierarchy();

        $this->command->info('Активности успешно добавлены! ' . Activity::count());
        $this->command->info('Уровень 1: ' . Activity::where('level', 1)->count());
        $this->command->info('Уровень 2: ' . Activity::where('level', 2)->count());
        $this->command->info('Уровень 3: ' . Activity::where('level', 3)->count());
    }
}
