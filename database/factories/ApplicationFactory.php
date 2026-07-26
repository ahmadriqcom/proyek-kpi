<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        $code = strtoupper($this->faker->unique()->lexify('APP???'));
        return [
            'code' => $code,
            'name' => 'Aplikasi ' . $code,
            'description' => 'Sistem Informasi ' . $this->faker->words(3, true),
            'is_active' => true,
        ];
    }
}
