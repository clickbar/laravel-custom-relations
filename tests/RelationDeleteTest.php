<?php

use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Client;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Order;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Project;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Task;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\TaskWithSoftDelete;

it('can delete tasks of client via PowerRelation', function () {

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

    expect(Task::whereIn('order_id', $orders->pluck('id'))->count())->toBeGreaterThan(0);
    $otherTaskCount = Task::whereNotIn('order_id', $orders->pluck('id'))->count();

    $client->tasks()->delete();
    expect(Task::all())->toHaveCount($otherTaskCount);
    expect(Task::whereIn('order_id', $orders->pluck('id'))->get())->toBeEmpty();
});

it('can delete tasks of client via PowerRelation from Parent', function () {

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

    expect(Task::whereIn('order_id', $orders->pluck('id'))->count())->toBeGreaterThan(0);
    $otherTaskCount = Task::whereNotIn('order_id', $orders->pluck('id'))->count();

    $client->tasksFromParent()->delete();
    expect(Task::all())->toHaveCount($otherTaskCount);
    expect(Task::whereIn('order_id', $orders->pluck('id'))->get())->toBeEmpty();
});

it('can soft delete tasks of client via PowerRelation', function () {

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

    expect(TaskWithSoftDelete::whereIn('order_id', $orders->pluck('id'))->count())->toBeGreaterThan(0);
    $otherTaskCount = TaskWithSoftDelete::whereNotIn('order_id', $orders->pluck('id'))->count();

    $client->tasksWithSoftDelete()->delete();

    expect(TaskWithSoftDelete::all())->toHaveCount($otherTaskCount);
    expect(TaskWithSoftDelete::whereIn('order_id', $orders->pluck('id'))->get())->toBeEmpty();
    expect(TaskWithSoftDelete::whereIn('order_id', $orders->pluck('id'))->withTrashed()->get())->toHaveCount($tasks->count());
});

it('can soft delete tasks of client via PowerRelation from parent', function () {

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

    expect(TaskWithSoftDelete::whereIn('order_id', $orders->pluck('id'))->count())->toBeGreaterThan(0);
    $otherTaskCount = TaskWithSoftDelete::whereNotIn('order_id', $orders->pluck('id'))->count();

    $client->tasksWithSoftDeleteFromParent()->delete();

    expect(TaskWithSoftDelete::all())->toHaveCount($otherTaskCount);
    expect(TaskWithSoftDelete::whereIn('order_id', $orders->pluck('id'))->get())->toBeEmpty();
    expect(TaskWithSoftDelete::whereIn('order_id', $orders->pluck('id'))->withTrashed()->get())->toHaveCount($tasks->count());
});
