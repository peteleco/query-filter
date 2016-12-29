<?php namespace Peteleco\QueryFilter;

use Illuminate\Database\Eloquent\Model;

/**
 * Class QueryFilterTrait
 *
 * @package App\Queries
 */
trait QueryFilterTrait
{
    /**
     * @param             $query
     * @param QueryFilter $filters
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    /**
     * @param             $query
     * @param QueryFilter $filters
     * @param Model       $model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterModel($query, QueryFilter $filters, Model $model)
    {
        return $filters->applyModel($query, $model);
    }
}