<?php

namespace Clickbar\LaravelCustomRelations\Tests\TestClasses\Models;

use Clickbar\LaravelCustomRelations\Eloquent\Relation\CustomRelationSingle;
use Clickbar\LaravelCustomRelations\Traits\HasCustomRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasCustomRelation;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    /** @return BelongsTo<Order, self> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return CustomRelationSingle<Client> */
    public function client(): CustomRelationSingle
    {
        return $this->customRelationSingle(
            Client::class,
            function ($query) {
                $query
                    ->join('projects', 'clients.id', 'client_id')
                    ->join('orders', 'projects.id', 'project_id')
                    ->join('tasks', 'orders.id', 'order_id');
            }
        );
    }

    /** @return CustomRelationSingle<Client> */
    public function clientFromParent(): CustomRelationSingle
    {
        return $this->customRelationFromParentSingle(
            Client::class,
            function ($query) {
                $query
                    ->join('orders', 'orders.id', 'order_id')
                    ->join('projects', 'projects.id', 'project_id')
                    ->join('clients', 'clients.id', 'client_id');
            }
        );
    }
}
