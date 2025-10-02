<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationPhoneFactory extends Factory
{
    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'phone_number' => $this->generateRussianPhone(),
        ];
    }

    /**
     * @return string
     */
    private function generateRussianPhone(): string
    {
        $formats = [
            // Мобильные операторы России
            '+7-9%d%d-%d%d%d-%d%d-%d%d',
            '+7 (9%d%d) %d%d%d-%d%d-%d%d',
            '8-9%d%d-%d%d%d-%d%d-%d%d',

            // Городские номера
            '+7-%d%d%d-%d%d%d-%d%d-%d%d',
            '8 (%d%d%d) %d%d%d-%d%d-%d%d',
            '%d-%d%d%d-%d%d-%d%d',
        ];

        $format = $this->faker->randomElement($formats);

        $mobileCodes = [900, 901, 902, 903, 904, 905, 906, 908, 909, 910, 911, 912, 913, 914, 915, 916, 917, 918, 919, 920, 921, 922, 924, 925, 926, 927, 928, 929, 930, 931, 932, 933, 934, 936, 937, 938, 939, 950, 951, 952, 953, 958, 960, 961, 962, 963, 964, 965, 966, 967, 968, 969, 977, 978, 980, 981, 982, 983, 984, 985, 986, 987, 988, 989, 991, 992, 993, 994, 995, 996, 997, 999];

        $cityCodes = [495, 499, 812, 343, 383, 846, 4012, 351, 4232, 8452, 4742, 4872, 3012];

        if (str_contains($format, '9%d%d')) {
            // Мобильный номер
            $code = $this->faker->randomElement($mobileCodes);
            $codeStr = (string) $code;

            return sprintf(
                str_replace('9%d%d', $codeStr, $format),
                ...array_fill(0, substr_count($format, '%d') - 2, $this->faker->numberBetween(0, 9))
            );
        } else {
            // Городской номер
            $code = $this->faker->randomElement($cityCodes);
            $digits = array_merge(
                str_split((string) $code),
                array_fill(0, substr_count($format, '%d') - strlen((string) $code), $this->faker->numberBetween(0, 9))
            );

            return sprintf($format, ...$digits);
        }
    }

    /**
     * @return $this
     */
    public function mobile(): static
    {
        return $this->state(function (array $attributes) {
            $mobileCodes = [900, 901, 902, 903, 904, 905, 906, 908, 909, 910, 911, 912, 913, 914, 915, 916, 917, 918, 919, 920, 921, 922, 924, 925, 926, 927, 928, 929];
            $code = $this->faker->randomElement($mobileCodes);

            return [
                'phone_number' => sprintf(
                    '+7-%s-%d%d%d-%d%d-%d%d',
                    $code,
                    $this->faker->numberBetween(1, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9)
                ),
            ];
        });
    }

    /**
     * @return $this
     */
    public function landline(): static
    {
        return $this->state(function (array $attributes) {
            $cityCodes = [
                '495' => 'Москва',
                '812' => 'СПб',
                '343' => 'Екатеринбург',
                '383' => 'Новосибирск',
                '846' => 'Самара',
                '351' => 'Челябинск',
            ];

            $code = $this->faker->randomElement(array_keys($cityCodes));

            return [
                'phone_number' => sprintf(
                    '+7 (%s) %d%d%d-%d%d-%d%d',
                    $code,
                    $this->faker->numberBetween(1, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9)
                ),
            ];
        });
    }

    /**
     * @return $this
     */
    public function short(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'phone_number' => sprintf(
                    '%d-%d%d%d-%d%d-%d%d',
                    $this->faker->numberBetween(2, 8),
                    $this->faker->numberBetween(1, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9),
                    $this->faker->numberBetween(0, 9)
                ),
            ];
        });
    }

}
