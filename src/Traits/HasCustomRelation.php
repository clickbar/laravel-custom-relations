<?php

namespace Clickbar\LaravelCustomRelations\Traits;

use Clickbar\LaravelCustomRelations\Eloquent\Relation\CustomRelation;
use Clickbar\LaravelCustomRelations\Eloquent\Relation\CustomRelationSingle;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasCustomRelation
{
    /**
     * @template TModelClass of Model
     *
     * @param  class-string<TModelClass>  $related
     * @return CustomRelation<TModelClass>
     */
    public function customRelation(string $related, \Closure $relationQueryCallback, ?string $localKey = null): CustomRelation
    {
        /** @var Model */
        $relatedInstance = $this->newRelatedInstance($related);

        return new CustomRelation(
            parent: $this,
            relatedInstance: $relatedInstance,
            relationQueryCallback: $relationQueryCallback,
            localKey: $localKey ?? $this->getKeyName(),
            queryFromParent: false);
    }

    /**
     * @template TModelClass of Model
     *
     * @param  class-string<TModelClass>  $related
     * @return CustomRelation<TModelClass>
     */
    public function customRelationFromParent(string $related, \Closure $relationQueryCallback, ?string $localKey = null): CustomRelation
    {
        /** @var Model */
        $relatedInstance = $this->newRelatedInstance($related);

        return new CustomRelation(
            parent: $this,
            relatedInstance: $relatedInstance,
            relationQueryCallback: $relationQueryCallback,
            localKey: $localKey ?? $this->getKeyName(),
            queryFromParent: true);
    }

    /**
     * @template TModelClass of Model
     *
     * @param  class-string<TModelClass>  $related
     * @return CustomRelationSingle<TModelClass>
     */
    public function customRelationSingle(string $related, \Closure $relationQueryCallback, ?string $localKey = null): CustomRelationSingle
    {
        /** @var Model */
        $relatedInstance = $this->newRelatedInstance($related);

        /** @var CustomRelationSingle<TModelClass> */
        return new CustomRelationSingle(
            parent: $this,
            relatedInstance: $relatedInstance,
            relationQueryCallback: $relationQueryCallback,
            localKey: $localKey ?? $this->getKeyName(),
            queryFromParent: false);
    }

    /**
     * @template TModelClass of Model
     *
     * @param  class-string<TModelClass>  $related
     * @return CustomRelationSingle<TModelClass>
     */
    public function customRelationFromParentSingle(string $related, \Closure $relationQueryCallback, ?string $localKey = null): CustomRelationSingle
    {
        /** @var Model */
        $relatedInstance = $this->newRelatedInstance($related);

        /** @var CustomRelationSingle<TModelClass> */
        return new CustomRelationSingle(
            parent: $this,
            relatedInstance: $relatedInstance,
            relationQueryCallback: $relationQueryCallback,
            localKey: $localKey ?? $this->getKeyName(),
            queryFromParent: true);
    }
}
