<?php

namespace LAdmin\Package\Table\Instance;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use LAdmin\Package\Table\TableInterface;
use LAdmin\Package\Model\PresentableModelInterface;

/**
 * Table for working with collections of objects
 */
class ModelTable extends ModelCollectionTable
{

    protected $model;
    protected $query;

    /**
     * Specifying the model to query
     * Model should not have a backslash in the beginning
     *
     * @param  null
     *
     * @return TableInterface
     */
    public function model(String $model) : TableInterface
    {
        $this->model = $model;
        $this->buildQueryForModel();

        return $this;
    }

    /**
     * Building a query for the specified model
     *
     * @throws Exception if there is no model specified or the class does not exist
     *
     * @param  String $model = null
     *
     * @return TableInterface
     */
    public function buildQueryForModel(String $model = null) : TableInterface
    {
        if (is_null($model) === true)
        {
            if (is_null($this->model) === true)
            {
                throw new Exception('Model is not specified!');
            }

            $model = $this->model;
        }

        $modelClass = '\\' . $model;

        if (class_exists($model) === false)
        {
            throw new Exception("Model {$model} does not exist!");
        }

        $query = new $modelClass;

        $this->query = $query;

        if ($query instanceof PresentableModelInterface)
        {
            $this->buildPresentableModelDependencies();
        }

        return $this;
    }

    /**
     * Modifying the query for the model
     *
     * @param  Closure|Array $modify
     *
     * @return TableInterface
     *
     * @todo   Fix query modification procedure
     */
    public function modifyQuery($modify) : TableInterface
    {
        echo '<p>Modify query</p>';
        call_user_func($modify, $this->query);

        return $this;
    }

    /**
     * (@inheritdoc)
     */
    public function buildContents() : TableInterface
    {
        echo '<p>Get results</p>';
        $collection = $this->query->get();
        $this->setCollection($collection);

        return parent::buildContents();
    }

    /**
     * Builds all norally passed details using the PresentableModel configuration details
     *
     * @param  null
     *
     * @return TableInterface
     */
    public function buildPresentableModelDependencies() : TableInterface
    {
        $this->modifyQuery([$this->query, 'getQueryModifier']);
        $this->setColumns($this->query->getColumns());
        $this->setHeaders($this->query->getHeaders());
        $this->showHead($this->query->showHead());
        $this->showFoot($this->query->showFoot());

        return $this;
    }

}
