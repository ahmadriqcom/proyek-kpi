<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Region>
 */
class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        $city = $this->faker->unique()->city();
        return [
            'code' => strtoupper(substr($city, 0, 3)),
            'name' => 'Kota ' . $city,
        ];
    }
}
