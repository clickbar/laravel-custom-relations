<?php

namespace Clickbar\LaravelCustomRelations\Tests\TestClasses\Models;

use Clickbar\LaravelCustomRelations\Traits\HasCustomRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskWithSoftDelete extends Model
{
    use HasCustomRelation;
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tasks_with_soft_deletes';

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
}
