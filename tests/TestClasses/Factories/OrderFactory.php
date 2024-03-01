<?php

namespace Clickbar\LaravelCustomRelations\Tests\TestClasses\Factories;

use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'project_id' => Project::factory(),
        ];
    }
}
