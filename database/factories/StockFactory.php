<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;


class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //product_idは外部キー制約がかかっているので、productモデルでhasFactoryを呼び出しておき使用できるようにしておくこと。
            'product_id' => Product::factory(),
            'type' => $this->faker->numberBetween(1, 2),
            'quantity' => $this->faker->randomNumber,
        ];
    }
}
