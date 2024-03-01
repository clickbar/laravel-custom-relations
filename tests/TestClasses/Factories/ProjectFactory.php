<?php

namespace Clickbar\LaravelCustomRelations\Tests\TestClasses\Factories;

use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'client_id' => Client::factory(),
        ];
    }
}
