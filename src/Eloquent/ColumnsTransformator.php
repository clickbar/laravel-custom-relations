<?php

namespace Clickbar\LaravelCustomRelations\Eloquent;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use Illuminate\Support\Str;

/**
 * The Class is used to transform the $columns given to the getRelationExistenceQuery method of a power relation.
 */
class ColumnsTransformator
{
    const defaultAggregates = [
        'sum',
        'avg',
        'min',
        'max',
        'count',
    ];

    private static function isAggregate(string $sql): bool
    {
        foreach (self::defaultAggregates as $aggregate) {
            if (str_starts_with(strtolower($sql), strtolower($aggregate))) {
                return true;
            }
        }

        return false;
    }

    public static function transform(mixed $columns, string $tableName, Grammar $grammar): mixed
    {
        if (is_array($columns)) {
            return array_map(fn ($column) => self::transform($column, $tableName, $grammar), $columns);
        }

        if ($columns instanceof Expression) {
            return self::transform($columns->getValue($grammar), $tableName, $grammar);
        }

        if (is_string($columns) && self::isAggregate($columns)) {
            return new \Illuminate\Database\Query\Expression(self::transformAggregate($columns, $tableName));
        }

        return $columns;
    }

    private static function transformAggregate(string $sql, string $tableName): string
    {

        // Retrieve the column after the last "."
        $column = Str::of($sql)
            ->between('(', ')')
            ->afterLast('"."')
            ->trim('"')
            ->toString();

        // In case of only the * we do not need to prefix it with a table name
        if ($column === '*') {
            return $sql;
        }

        $aggregate = Str::before($sql, '(');

        return "$aggregate(\"$tableName\".\"$column\")";
    }
}
