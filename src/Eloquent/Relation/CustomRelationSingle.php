<?php

namespace Clickbar\LaravelCustomRelations\Eloquent\Relation;

/**
 * @template TModelClass of \Illuminate\Database\Eloquent\Model
 *
 * @extends CustomRelation<TModelClass>
 */
class CustomRelationSingle extends CustomRelation
{
    /**
     * Initialize the relation on a set of models.
     *
     * @param  string  $relation
     */
    public function initRelation(array $models, $relation): array
    {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }

        return $models;
    }

    public function getResults()
    {
        if ($this->getParentKey() === null) {
            return null;
        }

        return $this->query->first();
    }

    // ///////////////////////////////////////////////////////////
    // Helper Stuff
    // ///////////////////////////////////////////////////////////

    /**
     * Get the value of a relationship many type.
     *
     * @return mixed
     */
    protected function getRelationValue(array $dictionary, string $key)
    {
        $value = $dictionary[$key];

        return reset($value);
    }
}
