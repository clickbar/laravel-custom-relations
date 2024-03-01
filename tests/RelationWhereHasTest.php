<?php

use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Client;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Order;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Project;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Task;

it('can use whereHas on tasks of client via PowerRelation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    // Create the data we will expect
    $client = Client::factory()->create();
    $projects = Project::factory()->count(2)->recycle($client)->create();
    $orders = $projects->flatMap(fn (Project $project) => Order::factory()->count(2)->recycle($project)->create());
    $orders->flatMap(fn (Order $order) => Task::factory()->count(3)->recycle($order)->create(['name' => 'power²']));

    $clients = Client::whereHas('tasks', fn ($query) => $query->where('name', 'power²'))->get();

    expect($clients)->toHaveCount(1);
    expect($clients->firstOrFail()->id)->toBe($client->id);

});

it('can use whereHas on tasks of client via PowerRelation from Parent', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    // Create the data we will expect
    $client = Client::factory()->create();
    $projects = Project::factory()->count(2)->recycle($client)->create();
    $orders = $projects->flatMap(fn (Project $project) => Order::factory()->count(2)->recycle($project)->create());
    $orders->flatMap(fn (Order $order) => Task::factory()->count(3)->recycle($order)->create(['name' => 'power²']));

    $clients = Client::whereHas('tasksFromParent', fn ($query) => $query->where('name', 'power²'))->get();

    expect($clients)->toHaveCount(1);
    expect($clients->firstOrFail()->id)->toBe($client->id);

});

it('can use whereHas on client of tasks via PowerRelation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    // Create the data we will expect
    $client = Client::factory()->create(['name' => 'power-company²']);
    $projects = Project::factory()->count(2)->recycle($client)->create();
    $orders = $projects->flatMap(fn (Project $project) => Order::factory()->count(2)->recycle($project)->create());
    $tasks = $orders->flatMap(fn (Order $order) => Task::factory()->count(3)->recycle($order)->create());

    $powerCompanyTasks = Task::whereHas('client', fn ($query) => $query->where('name', 'power-company²'))
        ->orderBy('id')
        ->get();
    $tasks->sortBy('id');

    expect($powerCompanyTasks)->toHaveSameSize($tasks);

    $powerCompanyTasks->zip($tasks)->each(function ($data) {
        $whereHasTaskData = $data->get(0)->toArray();
        $taskData = $data->get(1)->toArray();

        ksort($taskData);
        ksort($whereHasTaskData);

        expect($whereHasTaskData)->toBe($taskData);
    });

});

it('can use whereHas on client of tasks via PowerRelation from Parent', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    // Create the data we will expect
    $client = Client::factory()->create(['name' => 'power-company²']);
    $projects = Project::factory()->count(2)->recycle($client)->create();
    $orders = $projects->flatMap(fn (Project $project) => Order::factory()->count(2)->recycle($project)->create());
    $tasks = $orders->flatMap(fn (Order $order) => Task::factory()->count(3)->recycle($order)->create());

    $powerCompanyTasks = Task::whereHas('clientFromParent', fn ($query) => $query->where('name', 'power-company²'))
        ->orderBy('id')
        ->get();
    $tasks->sortBy('id');

    expect($powerCompanyTasks)->toHaveSameSize($tasks);

    $powerCompanyTasks->zip($tasks)->each(function ($data) {
        $whereHasTaskData = $data->get(0)->toArray();
        $taskData = $data->get(1)->toArray();

        ksort($taskData);
        ksort($whereHasTaskData);

        expect($whereHasTaskData)->toBe($taskData);
    });

});
