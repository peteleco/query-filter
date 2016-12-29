<?php namespace Peteleco\QueryFilter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class QueryFilter
 *
 * @package App\Queries
 */
abstract class BaseQueryFilter
{

    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Builder
     */
    protected $builder;
    /**
     * @var int $perPage
     */
    private $perPage = 20;

    /**
     * QueryFilter constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Aplica utilizando um modelo
     *
     * @param Builder $builder
     * @param Model   $model
     *
     * @return Builder
     */
    public function applyModel(Builder $builder, Model $model)
    {
        $this->builder = $this->apply($builder);

        if (method_exists($this, $name = $this->getFilterModelName($model))) {
            call_user_func_array([$this, $name], array_filter([$model]));
        }

        return $this->builder;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function apply(Builder $builder)
    {
        $this->setBuilder($builder);

        foreach ($this->filters() as $name => $value) {
            if ($this->canApplyMethod($method = camel_case($name))) {
                call_user_func_array([$this, $method], array_filter([$this->filterMultiple($value)]));
            }
        }

        return $this->builder;
    }

    /**
     * @return array
     */
    public function filters()
    {
        return $this->request->all();
    }

    /**
     * Can only apply public methods
     *
     * @param $method
     *
     * @return bool
     */
    private function canApplyMethod($method)
    {
        if (! method_exists($this, $method)) {
            return false;
        }
        $reflection = new \ReflectionMethod($this, $method);

        if (! $reflection->isPublic()) {
            return false;
        }

        return true;
    }

    /**
     * Filtra por multiplos valores quebrando a
     * vírgula em array
     *
     * @param $value
     *
     * @return array
     */
    protected function filterMultiple($value)
    {
        return explode(',', $value);
//        if (strpos($value, ',') !== false) {
//            return explode(',', $value);
//        }
//        return 'chau';
    }

    /**
     * Retorna o nome do metodo chamado quando for filtrar pelo modelo
     *
     * @param Model $model
     *
     * @return string
     */
    private function getFilterModelName(Model $model)
    {
        return 'model' . class_basename($model);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Adiciona filtro sem ser pelo request
     *
     * @param $filter
     * @param $value
     *
     * @return $this
     */
    public function addFilter($filter, $value)
    {
        $this->request->query->add([$filter => $value]);

        return $this;
    }

    /**
     * Retorna os campos passados no filtro
     */
    public function fields()
    {
        return array_keys($this->filters());
    }

    /**
     * Seta o per page
     *
     * @param $value
     */
    public function perPage($value)
    {
        $this->setPerPage(array_first($value));
    }

    public function getPerPage()
    {
        return (int)$this->perPage;
    }

    public function setPerPage($value)
    {
        if ($value > 0 && $value < $this->perPage) {
            $this->perPage = $value;
        }
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @param Builder $builder
     *
     * @return QueryFilter
     */
    public function setBuilder($builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     *
     */
    public function resetRequest()
    {
        $this->request = new Request();
    }
}
