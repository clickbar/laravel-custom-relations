<?php

use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Client;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Order;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Project;
use Clickbar\LaravelCustomRelations\Tests\TestClasses\Models\Task;
use Illuminate\Support\Facades\DB;

it('throws error when trying to make a record on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->make(['name' => 'Super Client']);

})->throws(Exception::class, 'Power Relation can be used read only!');

it('throws error when trying to create a record on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->create(['name' => 'Super Client']);

})->throws(Exception::class, 'Power Relation can be used read only!');

it('throws error when trying to force create a record on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->forceCreate(['name' => 'Super Client']);

})->throws(Exception::class, 'Power Relation can be used read only!');

it('throws error when trying to force create quietly a record on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->forceCreateQuietly(['name' => 'Super Client']);

})->throws(Exception::class, 'Power Relation can be used read only!');

it('throws error when trying to insert records on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->insert(['name' => 'Super Client']);

})->throws(Exception::class, 'Power Relation can be used read only!');

it('throws error when trying to insertGetId records on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->insertGetId(['name' => 'Super Client']);

})->throws(Exception::class, 'Power Relation can be used read only!');

it('throws error when trying to insertOrIgnore records on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->insertOrIgnore(['name' => 'Super Client']);

})->throws(Exception::class, 'Power Relation can be used read only!');

it('throws error when trying to insertOrIgnoreUsing records on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->insertOrIgnoreUsing(['name' => 'Super Client'], DB::query());

})->throws(Exception::class, 'Power Relation can be used read only!');

it('throws error when trying to insertUsing records on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->insertUsing(['name' => 'Super Client'], DB::query());

})->throws(Exception::class, 'Power Relation can be used read only!');

it('throws error when trying to updateOrCreate records on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->updateOrCreate(['id' => 1], ['name' => 'test']);

})->throws(Exception::class, 'Power Relation can be used read only!');

it('throws error when trying to updateOrInsert records on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->updateOrInsert(['name' => 'Super Client'], ['id' => 1]);

})->throws(Exception::class, 'Power Relation can be used read only!');

it('throws error when trying to update records on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->update(['name' => 'Super Client']);

})->throws(Exception::class, 'Power Relation can be used read only!');

it('throws error when trying to force delete records on relation', function () {

    // Create some noise
    Client::factory()->count(3)
        ->has(Project::factory()->count(7)
            ->has(Order::factory()->count(4)
                ->has(Task::factory()->count(8))
            )
        )
        ->create();

    /** @var Task $task */
    $task = Task::first();
    $task->client()->forceDelete();

})->throws(Exception::class, 'Power Relation can be used read only!');
