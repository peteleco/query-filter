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
     * @param                 $query
     * @param BaseQueryFilter $filters
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeFilter($query, BaseQueryFilter $filters)
    {
        return $filters->apply($query);
    }

    /**
     * @param                 $query
     * @param BaseQueryFilter $filters
     * @param Model           $model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterModel($query, BaseQueryFilter $filters, Model $model)
    {
        return $filters->applyModel($query, $model);
    }
}