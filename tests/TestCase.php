<?php

namespace Clickbar\LaravelCustomRelations\Tests;

use Clickbar\LaravelCustomRelations\LaravelCustomRelationsServiceProvider;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessModelNamesUsing(
            fn (Factory $factory) => 'Clickbar\\LaravelCustomRelations\\Tests\\TestClasses\\Models\\'.Str::before(class_basename($factory::class), 'Factory')
        );
        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Clickbar\\LaravelCustomRelations\\Tests\\TestClasses\\Factories\\'.class_basename($modelName).'Factory'
        );

        /** @var DatabaseManager $db */
        $db = $this->app->get('db');

        $db->connection()->getSchemaBuilder()->create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        $db->connection()->getSchemaBuilder()->create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('client_id')->index()->constrained();
            $table->timestamps();
        });

        $db->connection()->getSchemaBuilder()->create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('project_id')->index()->constrained();
            $table->timestamps();
        });

        $db->connection()->getSchemaBuilder()->create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('order_id')->index()->constrained();
            $table->timestamps();
        });

        $db->connection()->getSchemaBuilder()->create('tasks_with_soft_deletes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('order_id')->index()->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelCustomRelationsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        //config()->set('database.default', 'testing');
    }
}
