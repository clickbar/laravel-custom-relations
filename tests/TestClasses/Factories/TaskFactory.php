<?php

namespace Clickbar\LaravelCustomRelations\Tests\TestClasses\Factories;

use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'order_id' => Order::factory(),
        ];
    }
}
