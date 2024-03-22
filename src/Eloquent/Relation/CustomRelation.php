<?php

namespace Clickbar\LaravelCustomRelations\Eloquent\Relation;

use Clickbar\LaravelCustomRelations\Eloquent\ColumnsTransformator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @template TModelClass of \Illuminate\Database\Eloquent\Model
 *
 * @extends Relation<TModelClass>
 *
 * @mixin Builder<TModelClass>
 */
class CustomRelation extends Relation
{
    public function __construct(
        Model $parent,
        protected Model $relatedInstance,
        /** The callback that is used to add constraints to the relation.*/
        protected \Closure $relationQueryCallback,
        /** The local key of the parent model. */
        protected string $localKey,
        /** Indicates whether the query should start from the parent instead from the related instance */
        protected bool $queryFromParent,
    ) {
        $query = $this->relatedInstance->newQuery();
        if ($queryFromParent) {
            $query->from($parent->getTable());
        }

        parent::__construct($query, $parent);

        /*
         * Override the related instance. The regular Relation uses the query to get the related Model.
         * In our case, we use the query starting from the parent.
         * Without override the created models would be of the parent model instead of the related one
         */
        $this->related = $this->relatedInstance;
    }

    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->select($this->getQualifiedRelatedTableColumn('*'));
            call_user_func($this->relationQueryCallback, $this->query);
            $this->query->whereNotNull($this->getQualifiedRelatedTableColumn($this->relatedInstance->getKeyName()));
            $this->query->where($this->getQualifiedParentKeyName(), $this->parent->getKey());
        }
    }

    public function addEagerConstraints(array $models)
    {
        $this->query->select($this->getQualifiedRelatedTableColumn('*'));
        call_user_func($this->relationQueryCallback, $this->query);
        $this->query->whereNotNull($this->getQualifiedRelatedTableColumn($this->relatedInstance->getKeyName()));
        $this->query->whereIn($this->getQualifiedParentKeyName(), array_map(fn (Model $model) => $model->getKey(), $models));
        // Add the qualified parent key name for building the dictionary.
        // Use AS again, to prevent the related id field from being overwritten
        // Only with the alias the attribute will be tableName.idColumn
        $this->query->addSelect("{$this->getQualifiedParentKeyName()} AS {$this->getQualifiedParentKeyName()}");
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  string  $relation
     */
    public function initRelation(array $models, $relation): array
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /**
     * @param  Collection<int, Model>  $results
     */
    public function match(array $models, Collection $results, $relation): array
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {
            if (isset($dictionary[$key = $model->getAttribute($this->localKey)])) {
                $model->setRelation(
                    $relation,
                    $this->getRelationValue($dictionary, $key)
                );
            }
        }

        return $models;
    }

    public function getResults()
    {
        return $this->getParentKey() !== null
            ? $this->query->get()
            : $this->related->newCollection();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  Builder<Model>  $parentQuery
     * @return Builder<Model>
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*']): Builder
    {
        /*
         * There might come some aggregates with column specified, so we need to transform the table name.
         * Before transformation the table name is guessed by the $table of the target eloquent model.
         * Since we use a fromSub call, we need to change the table in the aggregate function.
         */
        $columns = ColumnsTransformator::transform($columns, 'dynamic_relation_correlated', $query->getGrammar());

        $subSelect = $this->relatedInstance->newQuery();
        if ($this->queryFromParent) {
            $subSelect->from($this->parent->getTable());
        }
        $subSelect->select("{$this->getQualifiedParentKeyName()} AS dynamic_relation_correlated_id");
        $subSelect->addSelect($this->getQualifiedRelatedTableColumn('*'));
        call_user_func($this->relationQueryCallback, $subSelect);

        $query->fromSub($subSelect, 'dynamic_relation_correlated');

        $query->select($columns);

        return $query->whereColumn(
            $this->getQualifiedParentKeyName(), '=', 'dynamic_relation_correlated.dynamic_relation_correlated_id'
        );
    }

    // ///////////////////////////////////////////////////////////
    // Helper Stuff
    // ///////////////////////////////////////////////////////////

    /**
     * Get the key value of the parent's local key.
     */
    protected function getParentKey(): mixed
    {
        return $this->parent->getAttribute($this->localKey);
    }

    /**
     * Quantifies the given column with the name of the related table
     */
    protected function getQualifiedRelatedTableColumn(string $column): string
    {
        return "{$this->relatedInstance->getTable()}.{$column}";
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param  Collection<int, Model>  $results
     */
    protected function buildDictionary(Collection $results): array
    {
        $foreign = $this->getQualifiedParentKeyName();

        return $results->mapToDictionary(function ($result) use ($foreign) {
            return [$result->{$foreign} => $result];
        })->all();
    }

    /**
     * Get the value of a relationship many type.
     *
     * @return mixed
     */
    protected function getRelationValue(array $dictionary, string $key)
    {
        $value = $dictionary[$key];

        return $this->related->newCollection($value);
    }

    // ///////////////////////////////////////////////////////////
    // Overrides forcing read only
    // ///////////////////////////////////////////////////////////

    public function make(array $attributes = []): void
    {
        $this->throwReadOnlyException();
    }

    public function create(array $attributes = []): void
    {
        $this->throwReadOnlyException();
    }

    public function forceCreateQuietly(array $attributes = [])
    {
        $this->throwReadOnlyException();
    }

    public function insertGetId(array $values, $sequence = null)
    {
        $this->throwReadOnlyException();
    }

    public function insert(array $values)
    {
        $this->throwReadOnlyException();
    }

    public function insertOrIgnore(array $values)
    {
        $this->throwReadOnlyException();
    }

    public function update(array $values): void
    {
        $this->throwReadOnlyException();
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        $this->throwReadOnlyException();
    }

    public function insertUsing(array $columns, $query)
    {
        $this->throwReadOnlyException();
    }

    public function updateOrInsert(array $attributes, array $values = [])
    {
        $this->throwReadOnlyException();
    }

    public function insertOrIgnoreUsing(array $columns, $query)
    {
        $this->throwReadOnlyException();
    }

    public function delete(mixed $id = null): void
    {
        /*
         * If we come from parent, the "from" property on the query is changed to the parent.
         * The default compile delete statement from laravel uses the "from" to determine
         * the table for deletion and also the prefix for the IN (select "from".id ...)
         * If we use the regular delete, it would hit a wrong table!!!
         *
         * => Custom Delete in case of queryFromParent == true
         */
        if ($this->queryFromParent) {
            $modelKeyName = $this->getQualifiedRelatedTableColumn($this->getModel()->getKeyName());

            $this->getModel()->query()
                ->whereIn($modelKeyName, $this->query->select($modelKeyName))
                ->delete();
        } else {
            parent::delete($id); // @phpstan-ignore-line
        }
    }

    public function forceCreate(array $attributes): void
    {
        $this->throwReadOnlyException();
    }

    public function forceDelete(): void
    {
        $this->throwReadOnlyException();
    }

    protected function throwReadOnlyException(): void
    {
        throw new \Exception('Power Relation can be used read only!');
    }
}
