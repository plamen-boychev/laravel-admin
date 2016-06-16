<?php

namespace LAdmin\Package\Table\Instance;

use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use LAdmin\Package\Table\BaseTable;
use LAdmin\Package\Table\TableInterface;
use LAdmin\Package\Table\Row\Row;

/**
 * Table for working with collections of objects
 */
class ModelCollectionTable extends BaseTable
{

    /**
     * @var EloquentCollection
     *
     * The collection to work with
     */
    protected $modelCollection;

    /**
     * @var String
     *
     * A string value to use as default when the specified property is not set
     */
    protected $emptyPropertyPlaceholder = '-';

    /**
     * @var Array
     *
     * Maps model's properties as table columns
     */
    protected $modelPropertiesAsColumns;

    /**
     * @var Array
     *
     * Labels for the table's head
     */
    protected $headerLabels = [];

    /**
     * {@inheritdoc}
     */
    public function strigifyTableContents() : String
    {
        $this->buildContents();

        return parent::strigifyTableContents();
    }

    /**
     * Triggering re-building the contents of the table, using the properties:
     * @var $modelCollection, @var $modelPropertiesAsColumns, @var $showFoot, @var $showHead
     *
     * Used just before the table is being rendered
     *
     * @param  null
     *
     * @return TableInterface
     */
    public function buildContents() : TableInterface
    {
        $this->buildHead();
        $this->buildFoot();
        $this->buildBody();

        return $this;
    }

    /**
     * Building the head section of the table, based on the table's configuration
     *
     * @param  null
     *
     * @return TableInterface
     */
    protected function buildHead() : TableInterface
    {
        if ($this->showHead !== true)
        {
            return $this;
        }

        $headersRow = [];
        foreach ($this->modelPropertiesAsColumns as $property)
        {
            if (isset($this->headerLabels[$property]) === false)
            {
                throw new Exception("No label specifier for column [{$property}]!");
            }

            $label = $this->headerLabels[$property];
            $headersRow[$property] = $label;
        }

        $this->head->buildContentFromArray([$headersRow]);

        return $this;
    }

    /**
     * Building the foot section of the table, based on the table's configuration
     *
     * @param  null
     *
     * @return TableInterface
     */
    protected function buildFoot() : TableInterface
    {
        if ($this->showFoot !== true)
        {
            return $this;
        }

        $headersRow = [];
        foreach ($this->modelPropertiesAsColumns as $property)
        {
            if (isset($this->headerLabels[$property]) === false)
            {
                throw new Exception("No label specifier for column [{$property}]!");
            }

            $label = $this->headerLabels[$property];
            $headersRow[$property] = $label;
        }

        $this->foot->buildContentFromArray([$headersRow]);

        return $this;
    }

    /**
     * Building the body section of the table, based on the table's configuration
     *
     * @param  null
     *
     * @return TableInterface
     */
    protected function buildBody() : TableInterface
    {
        foreach ($this->modelCollection as $index => $entry)
        {
            $row = new Row;
            $rowData = [];

            foreach ($this->modelPropertiesAsColumns as $property)
            {
                $propertyValue = null;
                $getterNames = [
                    'is' . ucfirst($property),
                    'has' . ucfirst($property),
                    'get' . ucfirst($property),
                ];

                foreach ($getterNames as $getter)
                {
                    if (method_exists($entry, $getter))
                    {
                        $propertyValue = $entry->{$getter}();
                        break;
                    }
                }

                $modelAttrbutes = $entry->getAttributes();
                if (is_null($propertyValue) === true && isset($modelAttrbutes[$property]))
                {
                    $propertyValue = $entry->{$property};
                }

                if (is_null($propertyValue) === true)
                {
                    throw new Exception("Could not access property [{$property}]!");
                }

                $rowData[$property] = $propertyValue;
            }

            $row->setContentFromArray($rowData);

            $this->body->addIndexedRow($index, $row);
        }

        return $this;
    }


    /**
     * Injecting the collection to work with
     *
     * @param  EloquentCollection $collection
     *
     * @return TableInterface
     */
    public function setCollection(EloquentCollection $collection) : TableInterface
    {
        $this->modelCollection = $collection;

        return $this;
    }

    /**
     * Setting columns to be listed in the table
     *
     * @param  Array $columns
     *
     * @return TableInterface
     */
    public function setColumns(Array $columns) : TableInterface
    {
        $this->modelPropertiesAsColumns = $columns;

        return $this;
    }

    /**
     * Setting the table heading labels for the table columns
     *
     * @param  Array $headerLabels
     *
     * @return TableInterface
     */
    public function setHeaders(Array $headerLabels) : TableInterface
    {
        $this->headerLabels = $headerLabels;

        return $this;
    }

}
