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
    protected $modelInstance;
    protected $query;
    protected $modelOptions = [];

    /**
     * Specifying the model to query
     * Model should not have a backslash in the beginning
     *
     * @param  String $model
     * @param  Array $options = null
     *
     * @return TableInterface
     */
    public function model(String $model, Array $options = null) : TableInterface
    {
        $this->model = $model;
        $this->modelOptions = $options;
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

        $modelInstance = $query = new $modelClass;

        $this->query = $query;
        $this->modelInstance = $modelInstance;

        if (isset($this->modelOptions['scope']) === true)
        {
            $this->applyModelScope();
        }

        if ($query instanceof PresentableModelInterface)
        {
            $this->buildPresentableModelDependencies();
        }

        return $this;
    }

    /**
     * Applying a model scope, specified in the options passed to self::model() as second argument
     *
     * @param  null
     *
     * @return TableInterface
     */
    protected function applyModelScope() : TableInterface
    {
        $scope = $this->modelOptions['scope'];

        $this->query->{$scope}();
        $this->modelInstance->{$scope}();

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
        call_user_func_array($modify, [&$this->query]);

        return $this;
    }

    /**
     * (@inheritdoc)
     */
    public function buildContents() : TableInterface
    {
        $this->buildCollection();

        return parent::buildContents();
    }

    /**
     * Builds the collection for the table
     *
     * @param  null
     *
     * @return TableInterface
     */
    protected function buildCollection() : TableInterface
    {
        $collection = $this->query->get();
        $this->setCollection($collection);

        return $this;
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
        $this->modifyQuery([$this->modelInstance, 'tableQueryModifier']);
        $this->setColumns($this->modelInstance->getColumns());
        $this->setHeaders($this->modelInstance->getHeaders());
        $this->showHead($this->modelInstance->showHead());
        $this->showFoot($this->modelInstance->showFoot());

        return $this;
    }

    /**
     * Relation string is being loaded from {relationClass}::__toString() or
     * {relationClass}::getColumnValueFor{ModelClassName}[scopeName]()
     * ModelClassName & scopeName should be in studly case
     * {@inheritdoc}
     */
    protected function getRelationPrintableValue($relatedEntry, $entry = null) : string
    {
        // $relationString = $this->normalizeString(get_class($relatedEntry));
        $entryString = $this->normalizeString(get_class($entry));
        $scopeString = $this->normalizeString($this->modelOptions['scope']);
        $scopeString = $scopeString ?? '';

        $getter = "getColumnValueFor{$entryString}{$scopeString}";

        if (method_exists($relatedEntry, $getter)) {
            return $relatedEntry->{$getter}();
        } else {
            return $this->fallbackRelationPritableValue($relatedEntry, $entry);
        }
    }

    /**
     * Stringying collection, resulted from chained getter
     *
     * @param  mixed  $entry
     * @param  string $propertyName
     * @param  EloquentCollection|BaseCollection $collection
     *
     * @return string
     */
    public function strigifyCollectionResultFromChainGetter($entry, string $propertyName, $collection) : string
    {
        // $relationString = $this->normalizeString(get_class($relatedEntry));
        $entryString = $this->normalizeString(get_class($entry));
        $scopeString = $this->normalizeString($this->modelOptions['scope']);
        $scopeString = $scopeString ?? '';

        $getter = 'property' . "{$entryString}{$scopeString}" . 'ListToString';

        if (method_exists($entry, $getter) === false)
        {
            return parent::strigifyCollectionResultFromChainGetter($entry, $propertyName, $collection);
        }

        $printableValue = $entry->{$getter}($collection);

        return $printableValue;
    }

}
