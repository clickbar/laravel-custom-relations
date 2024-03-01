<?php

use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Client;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Order;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Project;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Task;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\TaskWithSoftDelete;

it('can retrieve tasks of client via PowerRelation', function () {

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
    $tasks = $orders->flatMap(fn (Order $order) => Task::factory()->count(3)->recycle($order)->create());

    $clientTasks = $client->tasks;
    $clientTasksFromParent = $client->tasksFromParent;

    // Check if the same amount was retrieved
    expect($clientTasks)->toHaveCount($tasks->count());
    expect($clientTasksFromParent)->toHaveCount($tasks->count());

    // Compare the task with its key => values
    $clientTasks->zip($tasks)->each(function ($data) {
        $clientTaskData = $data->get(0)->toArray();
        $taskData = $data->get(1)->toArray();

        ksort($taskData);
        ksort($clientTaskData);

        expect($taskData)->toBe($clientTaskData);
    });

    $clientTasksFromParent->zip($tasks)->each(function ($data) {
        $clientTaskData = $data->get(0)->toArray();
        $taskData = $data->get(1)->toArray();

        ksort($taskData);
        ksort($clientTaskData);

        expect($taskData)->toBe($clientTaskData);
    });
});

it('can retrieve client of task via PowerRelation', function () {

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
    $tasks = $orders->flatMap(fn (Order $order) => Task::factory()->count(3)->recycle($order)->create());

    $clientData = $client->toArray();
    ksort($clientData);

    foreach ($tasks as $task) {

        $taskClient = $task->client;
        $taskClientFromParent = $task->clientFromParent;

        expect($taskClient)->toBeInstanceOf(Client::class);
        expect($taskClientFromParent)->toBeInstanceOf(Client::class);

        $taskClientData = $taskClient->toArray();
        $taskClientFromParentData = $taskClientFromParent->toArray();

        ksort($taskClientData);
        ksort($taskClientFromParentData);

        expect($taskClientData)->toBe($clientData);
        expect($taskClientFromParentData)->toBe($clientData);
    }
});

it('can retrieve tasks of client via PowerRelation properly handling soft deletes', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(TaskWithSoftDelete::factory()->count(8), 'tasksWithSoftDelete')
            )
        )
        ->create();

    // Create the data we will expect
    $client = Client::factory()->create();
    $projects = Project::factory()->count(2)->recycle($client)->create();
    $orders = $projects->flatMap(fn (Project $project) => Order::factory()->count(2)->recycle($project)->create());
    $tasks = $orders->flatMap(fn (Order $order) => TaskWithSoftDelete::factory()->count(3)->recycle($order)->create());

    $deletedTask = $tasks->first();
    $tasksWithoutDeleted = $tasks->skip(1);
    $deletedTask->delete();

    $clientTasks = $client->tasksWithSoftDelete;
    $clientTasksFromParent = $client->tasksWithSoftDeleteFromParent;

    // Check if the same amount was retrieved
    expect($clientTasks)->toHaveCount($tasksWithoutDeleted->count());
    expect($clientTasksFromParent)->toHaveCount($tasksWithoutDeleted->count());

    // Compare the task with its key => values
    $clientTasks->zip($tasksWithoutDeleted)->each(function ($data) {
        $clientTaskData = $data->get(0)->toArray();
        $taskData = $data->get(1)->toArray();

        ksort($taskData);
        ksort($clientTaskData);

        expect($taskData)->toBe($clientTaskData);
    });

    $clientTasksFromParent->zip($tasksWithoutDeleted)->each(function ($data) {
        $clientTaskData = $data->get(0)->toArray();
        $taskData = $data->get(1)->toArray();

        ksort($taskData);
        ksort($clientTaskData);

        expect($taskData)->toBe($clientTaskData);
    });

    // With Trashed
    $clientTasks = $client->tasksWithSoftDelete()->withTrashed()->get();
    $clientTasksFromParent = $client->tasksWithSoftDeleteFromParent()->withTrashed()->get();

    // Check if the same amount was retrieved
    expect($clientTasks)->toHaveCount($tasks->count());
    expect($clientTasksFromParent)->toHaveCount($tasks->count());

    // Compare the task with its key => values
    $clientTasks->zip($tasks)->each(function ($data) {
        $clientTaskData = $data->get(0)->toArray();
        $taskData = $data->get(1)->toArray();

        ksort($taskData);
        ksort($clientTaskData);

        expect($taskData)->toBe($clientTaskData);
    });

    $clientTasksFromParent->zip($tasks)->each(function ($data) {
        $clientTaskData = $data->get(0)->toArray();
        $taskData = $data->get(1)->toArray();

        ksort($taskData);
        ksort($clientTaskData);

        expect($taskData)->toBe($clientTaskData);
    });

});
