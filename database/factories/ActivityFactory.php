<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        $activities = $this->getHierarchyNames();

        $level = $this->faker->randomElement([1, 2, 3]);
        $parentId = $level === 1 ? null : Activity::factory()->state(['level' => $level - 1]);

        return [
            'name' => $this->faker->randomElement($activities[$level]),
            'parent_id' => $parentId,
            'level' => $level,
        ];
    }

    /**
     * @return array Activity
     */
    public function createHierarchy(): array
    {
        // Уровень 1: Корневые элементы
        $level1 = [
            'Еда' => $this->create(['name' => 'Еда', 'parent_id' => null, 'level' => 1]),
            'Автомобили' => $this->create(['name' => 'Автомобили', 'parent_id' => null, 'level' => 1]),
            'Строительство' => $this->create(['name' => 'Строительство', 'parent_id' => null, 'level' => 1]),
            'Информационные технологии' => $this->create(['name' => 'Информационные технологии', 'parent_id' => null, 'level' => 1]),
            'Медицина' => $this->create(['name' => 'Медицина', 'parent_id' => null, 'level' => 1]),
        ];

        // Уровень 2: Подкатегории
        $level2 = [
            'Еда' => [
                'Мясная продукция' => $this->create(['name' => 'Мясная продукция', 'parent_id' => $level1['Еда']->id, 'level' => 2]),
                'Молочная продукция' => $this->create(['name' => 'Молочная продукция', 'parent_id' => $level1['Еда']->id, 'level' => 2]),
                'Хлебобулочные изделия' => $this->create(['name' => 'Хлебобулочные изделия', 'parent_id' => $level1['Еда']->id, 'level' => 2]),
            ],
            'Автомобили' => [
                'Грузовые' => $this->create(['name' => 'Грузовые', 'parent_id' => $level1['Автомобили']->id, 'level' => 2]),
                'Легковые' => $this->create(['name' => 'Легковые', 'parent_id' => $level1['Автомобили']->id, 'level' => 2]),
                'Запчасти' => $this->create(['name' => 'Запчасти', 'parent_id' => $level1['Автомобили']->id, 'level' => 2]),
                'Аксессуары' => $this->create(['name' => 'Аксессуары', 'parent_id' => $level1['Автомобили']->id, 'level' => 2]),
            ],
            'Строительство' => [
                'Жилищное строительство' => $this->create(['name' => 'Жилищное строительство', 'parent_id' => $level1['Строительство']->id, 'level' => 2]),
                'Коммерческое строительство' => $this->create(['name' => 'Коммерческое строительство', 'parent_id' => $level1['Строительство']->id, 'level' => 2]),
            ],
            'Информационные технологии' => [
                'Веб-разработка' => $this->create(['name' => 'Веб-разработка', 'parent_id' => $level1['Информационные технологии']->id, 'level' => 2]),
                'Мобильная разработка' => $this->create(['name' => 'Мобильная разработка', 'parent_id' => $level1['Информационные технологии']->id, 'level' => 2]),
            ],
            'Медицина' => [
                'Стоматология' => $this->create(['name' => 'Стоматология', 'parent_id' => $level1['Медицина']->id, 'level' => 2]),
                'Терапия' => $this->create(['name' => 'Терапия', 'parent_id' => $level1['Медицина']->id, 'level' => 2]),
            ],
        ];

        // Уровень 3: Детальные подкатегории
        $level3 = [
            'Мясная продукция' => [
                $this->create(['name' => 'Говядина', 'parent_id' => $level2['Еда']['Мясная продукция']->id, 'level' => 3]),
                $this->create(['name' => 'Свинина', 'parent_id' => $level2['Еда']['Мясная продукция']->id, 'level' => 3]),
                $this->create(['name' => 'Птица', 'parent_id' => $level2['Еда']['Мясная продукция']->id, 'level' => 3]),
                $this->create(['name' => 'Колбасные изделия', 'parent_id' => $level2['Еда']['Мясная продукция']->id, 'level' => 3]),
            ],
            'Молочная продукция' => [
                $this->create(['name' => 'Молоко', 'parent_id' => $level2['Еда']['Молочная продукция']->id, 'level' => 3]),
                $this->create(['name' => 'Сыры', 'parent_id' => $level2['Еда']['Молочная продукция']->id, 'level' => 3]),
                $this->create(['name' => 'Йогурты', 'parent_id' => $level2['Еда']['Молочная продукция']->id, 'level' => 3]),
                $this->create(['name' => 'Масло', 'parent_id' => $level2['Еда']['Молочная продукция']->id, 'level' => 3]),
            ],
            'Хлебобулочные изделия' => [
                $this->create(['name' => 'Хлеб', 'parent_id' => $level2['Еда']['Хлебобулочные изделия']->id, 'level' => 3]),
                $this->create(['name' => 'Булочки', 'parent_id' => $level2['Еда']['Хлебобулочные изделия']->id, 'level' => 3]),
                $this->create(['name' => 'Торты', 'parent_id' => $level2['Еда']['Хлебобулочные изделия']->id, 'level' => 3]),
            ],
            'Грузовые' => [
                $this->create(['name' => 'Грузовики', 'parent_id' => $level2['Автомобили']['Грузовые']->id, 'level' => 3]),
                $this->create(['name' => 'Автобусы', 'parent_id' => $level2['Автомобили']['Грузовые']->id, 'level' => 3]),
                $this->create(['name' => 'Спецтехника', 'parent_id' => $level2['Автомобили']['Грузовые']->id, 'level' => 3]),
            ],
            'Легковые' => [
                $this->create(['name' => 'Седаны', 'parent_id' => $level2['Автомобили']['Легковые']->id, 'level' => 3]),
                $this->create(['name' => 'Хэтчбеки', 'parent_id' => $level2['Автомобили']['Легковые']->id, 'level' => 3]),
                $this->create(['name' => 'Внедорожники', 'parent_id' => $level2['Автомобили']['Легковые']->id, 'level' => 3]),
            ],
            'Запчасти' => [
                $this->create(['name' => 'Двигатели', 'parent_id' => $level2['Автомобили']['Запчасти']->id, 'level' => 3]),
                $this->create(['name' => 'Тормозная система', 'parent_id' => $level2['Автомобили']['Запчасти']->id, 'level' => 3]),
                $this->create(['name' => 'Подвеска', 'parent_id' => $level2['Автомобили']['Запчасти']->id, 'level' => 3]),
            ],
            'Аксессуары' => [
                $this->create(['name' => 'Шины', 'parent_id' => $level2['Автомобили']['Аксессуары']->id, 'level' => 3]),
                $this->create(['name' => 'Диски', 'parent_id' => $level2['Автомобили']['Аксессуары']->id, 'level' => 3]),
                $this->create(['name' => 'Автомобильная электроника', 'parent_id' => $level2['Автомобили']['Аксессуары']->id, 'level' => 3]),
            ],
            'Жилищное строительство' => [
                $this->create(['name' => 'Квартиры', 'parent_id' => $level2['Строительство']['Жилищное строительство']->id, 'level' => 3]),
                $this->create(['name' => 'Частные дома', 'parent_id' => $level2['Строительство']['Жилищное строительство']->id, 'level' => 3]),
                $this->create(['name' => 'Коттеджи', 'parent_id' => $level2['Строительство']['Жилищное строительство']->id, 'level' => 3]),
            ],
            'Коммерческое строительство' => [
                $this->create(['name' => 'Офисные центры', 'parent_id' => $level2['Строительство']['Коммерческое строительство']->id, 'level' => 3]),
                $this->create(['name' => 'Торговые центры', 'parent_id' => $level2['Строительство']['Коммерческое строительство']->id, 'level' => 3]),
                $this->create(['name' => 'Склады', 'parent_id' => $level2['Строительство']['Коммерческое строительство']->id, 'level' => 3]),
            ],
            'Веб-разработка' => [
                $this->create(['name' => 'Frontend разработка', 'parent_id' => $level2['Информационные технологии']['Веб-разработка']->id, 'level' => 3]),
                $this->create(['name' => 'Backend разработка', 'parent_id' => $level2['Информационные технологии']['Веб-разработка']->id, 'level' => 3]),
                $this->create(['name' => 'Fullstack разработка', 'parent_id' => $level2['Информационные технологии']['Веб-разработка']->id, 'level' => 3]),
            ],
            'Мобильная разработка' => [
                $this->create(['name' => 'iOS разработка', 'parent_id' => $level2['Информационные технологии']['Мобильная разработка']->id, 'level' => 3]),
                $this->create(['name' => 'Android разработка', 'parent_id' => $level2['Информационные технологии']['Мобильная разработка']->id, 'level' => 3]),
                $this->create(['name' => 'Cross-platform', 'parent_id' => $level2['Информационные технологии']['Мобильная разработка']->id, 'level' => 3]),
            ],
            'Стоматология' => [
                $this->create(['name' => 'Терапевтическая стоматология', 'parent_id' => $level2['Медицина']['Стоматология']->id, 'level' => 3]),
                $this->create(['name' => 'Хирургическая стоматология', 'parent_id' => $level2['Медицина']['Стоматология']->id, 'level' => 3]),
                $this->create(['name' => 'Ортодонтия', 'parent_id' => $level2['Медицина']['Стоматология']->id, 'level' => 3]),
            ],
            'Терапия' => [
                $this->create(['name' => 'Кардиология', 'parent_id' => $level2['Медицина']['Терапия']->id, 'level' => 3]),
                $this->create(['name' => 'Неврология', 'parent_id' => $level2['Медицина']['Терапия']->id, 'level' => 3]),
                $this->create(['name' => 'Эндокринология', 'parent_id' => $level2['Медицина']['Терапия']->id, 'level' => 3]),
            ],
        ];

        return compact('level1', 'level2', 'level3');
    }

    /**
     * @return array
     */
    protected function getHierarchyNames(): array
    {
        return [
            1 => [
                'Еда',
                'Автомобили',
                'Строительство',
                'Информационные технологии',
                'Медицина',
            ],
            2 => [
                'Мясная продукция', 'Молочная продукция', 'Хлебобулочные изделия',
                'Грузовые', 'Легковые', 'Запчасти', 'Аксессуары',
                'Жилищное строительство', 'Коммерческое строительство',
                'Веб-разработка', 'Мобильная разработка',
                'Стоматология', 'Терапия',
            ],
            3 => [
                'Говядина', 'Свинина', 'Птица', 'Колбасные изделия',
                'Молоко', 'Сыры', 'Йогурты', 'Масло',
                'Хлеб', 'Булочки', 'Торты',
                'Грузовики', 'Автобусы', 'Спецтехника',
                'Седаны', 'Хэтчбеки', 'Внедорожники',
                'Двигатели', 'Тормозная система', 'Подвеска',
                'Шины', 'Диски', 'Автомобильная электроника',
                'Квартиры', 'Частные дома', 'Коттеджи',
                'Офисные центры', 'Торговые центры', 'Склады',
                'Frontend разработка', 'Backend разработка', 'Fullstack разработка',
                'iOS разработка', 'Android разработка', 'Cross-platform',
                'Терапевтическая стоматология', 'Хирургическая стоматология', 'Ортодонтия',
                'Кардиология', 'Неврология', 'Эндокринология',
            ],
        ];
    }
}
