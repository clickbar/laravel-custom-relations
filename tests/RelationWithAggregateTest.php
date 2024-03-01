<?php

use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Client;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Order;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Project;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Task;
use Illuminate\Support\Collection;

it('can use withCount on tasks of client via PowerRelation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    $clients = Client::withCount('tasks')->get();

    // Load it for the flat way for comparison
    $clients->load('projects.orders.tasks');

    foreach ($clients as $client) {
        /** @var Collection<int, Task> $tasksFromFlatWay */
        $tasksFromFlatWay = $client->tasks_from_relation_chain;

        expect($client->tasks_count)->toBe($tasksFromFlatWay->count());
    }

});

it('can use withCount on tasks of client via PowerRelation from Parent', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    $clients = Client::withCount('tasksFromParent')->get();

    // Load it for the flat way for comparison
    $clients->load('projects.orders.tasks');

    foreach ($clients as $client) {
        /** @var Collection<int, Task> $tasksFromFlatWay */
        $tasksFromFlatWay = $client->tasks_from_relation_chain;
        expect($client->tasks_from_parent_count)->toBe($tasksFromFlatWay->count());
    }

});

it('can use withSum on tasks of client via PowerRelation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    $clients = Client::withSum('tasks', 'id')->get();

    // Load it for the flat way for comparison
    $clients->load('projects.orders.tasks');

    foreach ($clients as $client) {
        /** @var Collection<int, Task> $tasksFromFlatWay */
        $tasksFromFlatWay = $client->tasks_from_relation_chain;

        expect($client->tasks_sum_id)->toEqual($tasksFromFlatWay->sum('id'));
    }

});

it('can use withSum on tasks of client via PowerRelation from Parent', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    $clients = Client::withSum('tasksFromParent', 'id')->get();

    // Load it for the flat way for comparison
    $clients->load('projects.orders.tasks');

    foreach ($clients as $client) {
        /** @var Collection<int, Task> $tasksFromFlatWay */
        $tasksFromFlatWay = $client->tasks_from_relation_chain;

        expect($client->tasks_from_parent_sum_id)->toEqual($tasksFromFlatWay->sum('id'));
    }

});

it('can use withAvg on tasks of client via PowerRelation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    $clients = Client::withAvg('tasks', 'id')->get();

    // Load it for the flat way for comparison
    $clients->load('projects.orders.tasks');

    foreach ($clients as $client) {
        /** @var Collection<int, Task> $tasksFromFlatWay */
        $tasksFromFlatWay = $client->tasks_from_relation_chain;

        expect($client->tasks_avg_id)->toEqual($tasksFromFlatWay->avg('id'));
    }

});

it('can use withAvg on tasks of client via PowerRelation from Parent', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    $clients = Client::withAvg('tasksFromParent', 'id')->get();

    // Load it for the flat way for comparison
    $clients->load('projects.orders.tasks');

    foreach ($clients as $client) {
        /** @var Collection<int, Task> $tasksFromFlatWay */
        $tasksFromFlatWay = $client->tasks_from_relation_chain;

        expect($client->tasks_from_parent_avg_id)->toEqual($tasksFromFlatWay->avg('id'));
    }

});

it('can use withMin on tasks of client via PowerRelation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    $clients = Client::withMin('tasks', 'id')->get();

    // Load it for the flat way for comparison
    $clients->load('projects.orders.tasks');

    foreach ($clients as $client) {
        /** @var Collection<int, Task> $tasksFromFlatWay */
        $tasksFromFlatWay = $client->tasks_from_relation_chain;

        expect($client->tasks_min_id)->toEqual($tasksFromFlatWay->min('id'));
    }

});

it('can use withMin on tasks of client via PowerRelation from Parent', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    $clients = Client::withMin('tasksFromParent', 'id')->get();

    // Load it for the flat way for comparison
    $clients->load('projects.orders.tasks');

    foreach ($clients as $client) {
        /** @var Collection<int, Task> $tasksFromFlatWay */
        $tasksFromFlatWay = $client->tasks_from_relation_chain;

        expect($client->tasks_from_parent_min_id)->toEqual($tasksFromFlatWay->min('id'));
    }

});

it('can use withMax on tasks of client via PowerRelation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    $clients = Client::withMax('tasks', 'id')->get();

    // Load it for the flat way for comparison
    $clients->load('projects.orders.tasks');

    foreach ($clients as $client) {
        /** @var Collection<int, Task> $tasksFromFlatWay */
        $tasksFromFlatWay = $client->tasks_from_relation_chain;

        expect($client->tasks_max_id)->toEqual($tasksFromFlatWay->max('id'));
    }

});

it('can use withMax on tasks of client via PowerRelation from Parent', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    $clients = Client::withMax('tasksFromParent', 'id')->get();

    // Load it for the flat way for comparison
    $clients->load('projects.orders.tasks');

    foreach ($clients as $client) {
        /** @var Collection<int, Task> $tasksFromFlatWay */
        $tasksFromFlatWay = $client->tasks_from_relation_chain;

        expect($client->tasks_from_parent_max_id)->toEqual($tasksFromFlatWay->max('id'));
    }

});
