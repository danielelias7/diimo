<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "sku"       => $this->faker->randomNumber(),
            "name"      => $this->faker->sentence(),
            "quantity"  =>$this->faker->randomDigit(),
            "price"     => $this->faker->randomFloat(2,0,1000),
            "description"=> $this->faker->text(),
            "image"     => $this->faker->imageUrl()
        ];
    }
}
