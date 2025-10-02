<?php

namespace Database\Factories;

use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        $organizationTypes = ['ООО', 'ИП', 'АО', 'ЗАО', 'ПАО', 'НКО', 'СПК'];

        $companyNames = [
            // Продуктовые компании
            'Рога и Копыта', 'Молочная Река', 'Мясной Двор', 'Хлебный Дом', 'Сладкий Мир',
            'Фермер Плюс', 'Деликатесы', 'Золотой Колос', 'Молочный Берег', 'Мясная Лавка',
            'Хлебозавод №1', 'Кондитер', 'Гурман', 'Домашний Вкус', 'Натурпродукт',

            // IT компании
            'Цифровые Решения', 'Код Мастер', 'Веб Студия', 'Айти Сервис', 'Программист Плюс',
            'Софт Дизайн', 'Технологии Будущего', 'Диджитал Лаб', 'Системы и Сети', 'Инновации',

            // Автомобильные
            'Авто Центр', 'Машина Сервис', 'Колесо', 'Автозапчасти', 'Гараж 24',
            'Мотор', 'Автодилер', 'Шиномонтаж', 'СТО Профи', 'Автомастер',

            // Строительные
            'СтройМонтаж', 'Дом Строй', 'Кирпич и К°', 'Ремонт Сервис', 'Стройка Плюс',
            'Фундамент', 'Новостройки', 'Архитектор', 'Отделка Эксперт', 'Строительный Альянс',

            // Медицинские
            'Медицина Плюс', 'Здоровье', 'Стоматология', 'Поликлиника', 'Мед Центр',
            'Доктор Плюс', 'Клиника Семейная', 'Здоровый Город', 'Медикал', 'Лечебница',

            // Образовательные
            'Образование', 'Знание', 'Учебный Центр', 'Академия', 'Институт Развития',
            'Курсы Плюс', 'Обучение Pro', 'Образовательный Центр', 'Университет', 'Школа Мастеров',

            // Финансовые
            'Финанс Групп', 'Банк Сервис', 'Кредит Плюс', 'Страхование', 'Инвест Групп',
            'Капитал', 'Финансовые Решения', 'Денежные Системы', 'Банковский Дом', 'Кредитный Союз',
        ];

        $type = $this->faker->randomElement($organizationTypes);
        $name = $this->faker->randomElement($companyNames);

        // Формируем полное название
        if ($type === 'ИП') {
            $fullName = "ИП {$this->faker->lastName} ({$name})";
        } else {
            $fullName = "{$type} \"{$name}\"";
        }

        return [
            'name' => $fullName,
            'building_id' => Building::factory(),
        ];
    }

    /**
     * @return $this
     */
    public function food(): static
    {
        return $this->state(function (array $attributes) {
            $foodNames = [
                'Рога и Копыта', 'Молочная Река', 'Мясной Двор', 'Хлебный Дом', 'Сладкий Мир',
                'Фермер Плюс', 'Деликатесы', 'Золотой Колос', 'Молочный Берег', 'Мясная Лавка'
            ];

            $type = $this->faker->randomElement(['ООО', 'ИП', 'АО']);
            $name = $this->faker->randomElement($foodNames);

            return [
                'name' => $type === 'ИП' ? "ИП {$this->faker->lastName} ({$name})" : "{$type} \"{$name}\"",
            ];
        });
    }

    /**
     * @return $this
     */
    public function tech(): static
    {
        return $this->state(function (array $attributes) {
            $techNames = [
                'Цифровые Решения', 'Код Мастер', 'Веб Студия', 'Айти Сервис', 'Программист Плюс',
                'Софт Дизайн', 'Технологии Будущего', 'Диджитал Лаб', 'Системы и Сети', 'Инновации'
            ];

            $type = $this->faker->randomElement(['ООО', 'АО']);
            $name = $this->faker->randomElement($techNames);

            return [
                'name' => "{$type} \"{$name}\"",
            ];
        });
    }

    /**
     * @return $this
     */
    public function automotive(): static
    {
        return $this->state(function (array $attributes) {
            $autoNames = [
                'Авто Центр', 'Машина Сервис', 'Колесо', 'Автозапчасти', 'Гараж 24',
                'Мотор', 'Автодилер', 'Шиномонтаж', 'СТО Профи', 'Автомастер'
            ];

            $type = $this->faker->randomElement(['ООО', 'ИП']);
            $name = $this->faker->randomElement($autoNames);

            return [
                'name' => $type === 'ИП' ? "ИП {$this->faker->lastName} ({$name})" : "{$type} \"{$name}\"",
            ];
        });
    }
}
