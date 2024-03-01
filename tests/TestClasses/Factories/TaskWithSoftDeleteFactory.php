<?php

namespace Clickbar\LaravelCustomRelations\Tests\TestClasses\Factories;

use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskWithSoftDeleteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'order_id' => Order::factory(),
            'deleted_at' => null,
        ];
    }

    public function deleted(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'deleted_at' => now(),
            ];
        });
    }
}
