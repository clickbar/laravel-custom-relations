<?php

namespace Clickbar\LaravelCustomRelations\Tests\TestClasses\Models;

use Clickbar\LaravelCustomRelations\Eloquent\Relation\CustomRelation;
use Clickbar\LaravelCustomRelations\Traits\HasCustomRelation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasCustomRelation;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    /** @return HasMany<Project> */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /** @return Attribute<\Illuminate\Database\Eloquent\Collection<int, Task>, never> */
    public function tasksFromRelationChain(): Attribute
    {
        return Attribute::get(fn () => $this->projects
            ->flatMap(fn (Project $project) => $project->orders
                ->flatMap(fn (Order $order) => $order->tasks))
        );
    }

    /** @return CustomRelation<Task> */
    public function tasks(): CustomRelation
    {
        return $this->customRelation(
            Task::class,
            function ($query) {
                $query
                    ->join('orders', 'orders.id', 'order_id')
                    ->join('projects', 'projects.id', 'project_id')
                    ->join('clients', 'clients.id', 'client_id');
            });
    }

    /** @return CustomRelation<Task> */
    public function tasksFromParent(): CustomRelation
    {
        return $this->customRelationFromParent(
            Task::class,
            function ($query) {
                $query
                    ->join('projects', 'clients.id', 'client_id')
                    ->join('orders', 'projects.id', 'project_id')
                    ->join('tasks', 'orders.id', 'order_id');
            },
        );
    }

    /** @return CustomRelation<TaskWithSoftDelete> */
    public function tasksWithSoftDelete(): CustomRelation
    {
        return $this->customRelation(
            TaskWithSoftDelete::class,
            function ($query) {
                $query
                    ->join('orders', 'orders.id', 'order_id')
                    ->join('projects', 'projects.id', 'project_id')
                    ->join('clients', 'clients.id', 'client_id');
            });
    }

    /** @return CustomRelation<TaskWithSoftDelete> */
    public function tasksWithSoftDeleteFromParent(): CustomRelation
    {
        return $this->customRelationFromParent(
            TaskWithSoftDelete::class,
            function ($query) {
                $query
                    ->join('projects', 'clients.id', 'client_id')
                    ->join('orders', 'projects.id', 'project_id')
                    ->join('tasks_with_soft_deletes', 'orders.id', 'order_id');
            },
        );
    }
}
