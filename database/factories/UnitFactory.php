<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'manager_name' => $this->faker->name(),
            'label' => $this->faker->sentence(4),
            'description' => $this->faker->text(50),
        ];
    }
}
