<?php

namespace LAdmin\Package\Table\Instance;

use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use LAdmin\Package\Table\BaseTable;
use LAdmin\Package\Table\TableInterface;
use LAdmin\Package\Table\Row\Row;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Table for working with collections of objects
 */
class ModelCollectionTable extends BaseTable
{

    const CHAIN_DELIMITER = '.';

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
                $propertyValue = $this->getRowColumnValue($entry, $property)
                    ?? $this->getEmptyColumnPlaceholder()
                ;

                $rowData[$property] = $this->getRowColumnValue($entry, $property);
            }

            $row->setContentFromArray($rowData);

            $this->body->addIndexedRow($index, $row);
        }

        return $this;
    }

    /**
     * Returns a printable value for an empty column value
     * This method could be overriden to throw an exception if all column values
     * are required for some reason
     *
     * @param  mixed $placeholder = null
     *
     * @return mixed - scalar value or printable object
     */
    protected function getEmptyColumnPlaceholder($placeholder = null)
    {
        return $placeholder
            ?? $this->emptyPropertyPlaceholder
        ;
    }

    /**
     * Loading a column's value from a model's entry
     *
     * @param  mixed  $entry
     * @param  String $property
     *
     * @return mixed - scalar value or printable object
     */
    public function getRowColumnValue($entry, String $property)
    {
        $hasDot = strpos($property, self::CHAIN_DELIMITER);
        if ($hasDot)
        {
            return $this->loadColumnValueFromChain($entry, $property);
        }

        return $this->loadEntityPropertyViaGetter($entry, $property)
            ?? $this->loadEntityPropertyDirectly($entry, $property)
            ?? $this->getEntityRelationPrintable($entry, $property)
        ;
    }

    /**
     * Loading a column's value from a model's chain of relations
     * Returns one of the following:
     *    - Method in following the convention {relationClass}::getColumnValueFor{ModelClassName}()
     *    - relation's property value
     *    - value, provided by a property's getter method
     *    - Implementated of {relationClass}::__toString()
     *
     * @param  mixed  $chainString
     * @param  String $relationName
     *
     * @return mixed - scalar value or printable object
     */
    protected function loadColumnValueFromChain($entry, String $chainString) : string
    {
        $morphingChain = $chain = explode(self::CHAIN_DELIMITER, $chainString);
        $chainLink = null;
        $chainResult = $entry;

        while ($chainLink = array_shift($morphingChain))
        {
            $chainResult = $this->moveToChainLink($chainLink, $chainResult);
        }

        var_dump($chainResult);

        // $relation = $entry->{$relationName}();
        // $relatedEntries = $relation->get();
        // $printableValue = [];
        die;

        // foreach ($relatedEntries as $index => $relatedEntry)
        // {
        //     $relatedEntryPrint = $this->getRelationPrintableValue($relatedEntry, $entry);
        //     array_push($printableValue, $relatedEntryPrint);
        // }

        // $printableValue = implode('', $printableValue);

        // return $printableValue;
    }

    /**
     * Loading a column's value from a model's chain of relations
     *
     * @param  mixed $chainLink
     * @param  mixed $chainResult
     *
     * @return mixed - relation, property or result of a method of the passed entry
     */
    protected function moveToChainLink($chainLink, $chainResult)
    {
        $step = $this->loadEntityPropertyViaGetter($chainResult, $chainLink)
            ??  $this->loadEntityPropertyDirectly($chainResult, $chainLink)
            ??  $this->getEntityRelation($chainResult, $chainLink)
        ;

        if ($step instanceof EloquentCollection && ((bool) $step->count()) === false)
        {
            $step = $step->shift();
        }

        return $step;
    }

    /**
     * Loading an entity's property's value using a property getter
     *
     * @param  mixed  $entry
     * @param  String $property
     *
     * @return mixed - scalar value or printable object
     */
    protected function loadEntityPropertyViaGetter($entry, String $property)
    {
        $propertyValue = null;

        $getterNames = [
            'is'  . ucfirst($property),
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

        return $propertyValue;
    }

    /**
     * Loading an entity's property accessing it directly
     *
     * @param  mixed  $entry
     * @param  String $property
     *
     * @return mixed - scalar value or printable object
     */
    protected function loadEntityPropertyDirectly($entry, String $property)
    {
        if (is_object($entry) === false)
        {
            return null;
        }
        if (method_exists($entry, 'getAttributes') === false)
        {
            return null;
        }

        $propertyValue = null;

        $modelAttrbutes = $entry->getAttributes();
        if (is_null($propertyValue) === true && isset($modelAttrbutes[$property]))
        {
            $propertyValue = $entry->{$property};
        }

        return $propertyValue;
    }

    /**
     * Loading the passed entity's relation
     *
     * @param  mixed  $entry
     * @param  String $relationName
     *
     * @return relation
     */
    protected function getEntityRelation($entry, String $relationName)
    {
        if (method_exists($entry, $relationName))
        {
            return null;
        }

        $relation = $entry->{$relationName}();

        if (is_null($relation) === true)
        {
            return null;
        }

        $isRelation = is_subclass_of($relation, '\\Illuminate\\Database\\Eloquent\\Relations\\Relation');

        if ($isRelation === false)
        {
            return null;
        }

        $relatedEntries = $relation->get();

        return $relatedEntries;
    }

    /**
     * Loading an entity's relation as a printable value
     * Relation string is being loaded from {relationClass}::__toString() or
     * {relationClass}::getColumnValueFor{ModelClassName}()
     *
     * @param  mixed  $entry
     * @param  String $relationName
     *
     * @return mixed - scalar value or printable object
     */
    protected function getEntityRelationPrintable($entry, String $relationName)
    {
        $relation = $entry->{$relationName}();
        $relatedEntries = $relation->get();
        $printableValue = [];

        foreach ($relatedEntries as $index => $relatedEntry)
        {
            $relatedEntryPrint = $this->getRelationPrintableValue($relatedEntry, $entry);
            array_push($printableValue, $relatedEntryPrint);
        }

        $printableValue = implode('', $printableValue);

        return $printableValue;
    }

    /**
     * Returns a printable value for the passed relation
     * Relation string is being loaded from {relationClass}::__toString() or
     * {relationClass}::getColumnValueFor{ModelClassName}()
     * ModelClassName should be in studly case
     *
     * @param  mixed $relatedEntry
     * @param  mixed $entry = null
     *
     * @return String
     */
    protected function getRelationPrintableValue($relatedEntry, $entry = null) : String
    {
        // $relationString = $this->normalizeString(get_class($relatedEntry));
        $entryString = $this->normalizeString(get_class($entry));

        $getter = "getColumnValueFor{$entryString}";

        if (method_exists($relatedEntry, $getter)) {
            return $relatedEntry->{$getter}();
        } else {
            return $this->fallbackRelationPritableValue($relatedEntry, $entry);
        }
    }

    /**
     * Returns default built of printable value for the relation of the entry
     *
     * @param  mixed $relatedEntry
     * @param  mixed $entry = null
     *
     * @return String
     */
    protected function fallbackRelationPritableValue($relatedEntry, $entry = null) : String
    {
        $value = [];

        foreach ($relatedEntry as $related)
        {
            array_push($value, (String) $related);
        }

        $value = implode('', $related);

        return $value;
    }

    protected function normalizeString(String $string)
    {
        $string = class_basename($string);
        $string = preg_replace("/[^A-Za-z0-9 ]/", '', $string);
        $string = studly_case($string);

        return $string;
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
