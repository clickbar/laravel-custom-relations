<?php

use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Client;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Order;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Project;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

it('can eager load tasks of client via PowerRelation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    DB::enableQueryLog();
    $clients = Client::with('tasks')->get();
    expect(DB::getQueryLog())->toHaveCount(2);

    // Load it for the flat way for comparison
    $clients->load('projects.orders.tasks');

    DB::flushQueryLog();
    foreach ($clients as $client) {
        /** @var Collection<int, Task> $tasksFromFlatWay */
        $tasksFromFlatWay = $client->tasks_from_relation_chain;
        /** @var Collection<int, Task> $tasksFromEagerLoad */
        $tasksFromEagerLoad = $client->tasks;

        expect($tasksFromFlatWay)->toHaveSameSize($tasksFromEagerLoad);

        // Compare the task with its key => values
        $tasksFromEagerLoad->zip($tasksFromFlatWay)->each(function ($data) {
            $eagerTaskData = $data->get(0)->toArray();
            $flatTaskData = $data->get(1)->toArray();

            ksort($flatTaskData);
            ksort($eagerTaskData);

            // Use toMatchArray, because the eager loads adds the clients.id property to the attributes
            expect($eagerTaskData)->toMatchArray($flatTaskData);
        });
    }
    expect(DB::getQueryLog())->toBeEmpty();

});

it('can eager load tasks of client via PowerRelation from Parent', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    DB::enableQueryLog();
    $clients = Client::with('tasksFromParent')->get();
    expect(DB::getQueryLog())->toHaveCount(2);

    // Load it for the flat way for comparison
    $clients->load('projects.orders.tasks');

    DB::flushQueryLog();
    foreach ($clients as $client) {
        /** @var Collection<int, Task> $tasksFromFlatWay */
        $tasksFromFlatWay = $client->tasks_from_relation_chain;
        /** @var Collection<int, Task> $tasksFromEagerLoad */
        $tasksFromEagerLoad = $client->tasksFromParent;

        expect($tasksFromFlatWay)->toHaveSameSize($tasksFromEagerLoad);

        // Compare the task with its key => values
        $tasksFromEagerLoad->zip($tasksFromFlatWay)->each(function ($data) {
            $eagerTaskData = $data->get(0)->toArray();
            $flatTaskData = $data->get(1)->toArray();

            ksort($flatTaskData);
            ksort($eagerTaskData);

            // Use toMatchArray, because the eager loads adds the clients.id property to the attributes
            expect($eagerTaskData)->toMatchArray($flatTaskData);
        });
    }
    expect(DB::getQueryLog())->toBeEmpty();

});
