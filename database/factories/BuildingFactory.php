<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingFactory extends Factory
{
    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        $cities = [
            'Москва' => [
                ['lat' => 55.7558, 'lng' => 37.6176],
                ['lat' => 55.7959, 'lng' => 37.6460],
                ['lat' => 55.6648, 'lng' => 37.6165],
                ['lat' => 55.8436, 'lng' => 37.7188],
                ['lat' => 55.7121, 'lng' => 37.6306],
                ['lat' => 55.6645, 'lng' => 37.6168], // 10.15 km относительно первой точке
            ],
            'Санкт-Петербург' => [
                ['lat' => 59.9311, 'lng' => 30.3609],
                ['lat' => 59.959, 'lng' => 30.3310], // 3.52 km
            ],
            'Новосибирск' => [
                ['lat' => 55.0084, 'lng' => 82.9357],
                ['lat' => 55.1084, 'lng' => 82.9452], // 11.14 km
            ],
        ];

        $cityName = $this->faker->randomElement(array_keys($cities));
        $cityCoordinates = $this->faker->randomElement($cities[$cityName]);

        if ($cityName === 'Москва' && $this->faker->boolean(30)) {
            $cityCoordinates = [
                'lat' => $this->faker->randomFloat(4, 55.664, 55.665),
                'lng' => $this->faker->randomFloat(4, 37.616, 37.617),
            ];
        }

        $streets = [
            'ул. Ленина', 'ул. Советская', 'ул. Центральная', 'ул. Мира', 'ул. Победы',
            'ул. Гагарина', 'ул. Пушкина', 'ул. Кирова', 'ул. Комсомольская', 'ул. Молодежная',
            'пр. Независимости', 'бул. Революции', 'наб. Волжская', 'пер. Садовый', 'ш. Московское',
        ];

        $street = $this->faker->randomElement($streets);
        $houseNumber = $this->faker->numberBetween(1, 150);
        $building = $this->faker->optional(0.3)->randomElement(['А', 'Б', 'В', '/1', '/2']);
        $office = $this->faker->optional(0.4)->numberBetween(1, 500);

        $address = "г. {$cityName}, {$street}, д. {$houseNumber}";
        if ($building) {
            $address .= $building;
        }
        if ($office) {
            $address .= ", оф. {$office}";
        }

        return [
            'address' => $address,
            'latitude' => $cityCoordinates['lat'],
            'longitude' => $cityCoordinates['lng'],
        ];
    }
}
