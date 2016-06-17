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
        return $this->loadColumnValueFromEntryUsingPropertyGetter($entry, $property)
            ?? $this->loadColumnValueFromEntryAccessingPropertyDirectly($entry, $property)
            ?? $this->loadColumnValueFromEntryRelation($entry, $property)
        ;
    }

    /**
     * Loading a column's value from a model's entry using property getter
     *
     * @param  mixed  $entry
     * @param  String $property
     *
     * @return mixed - scalar value or printable object
     */
    protected function loadColumnValueFromEntryUsingPropertyGetter($entry, String $property)
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

        return $propertyValue;
    }

    /**
     * Loading a column's value from a model's entry accessing property directly
     *
     * @param  mixed  $entry
     * @param  String $property
     *
     * @return mixed - scalar value or printable object
     */
    protected function loadColumnValueFromEntryAccessingPropertyDirectly($entry, String $property)
    {
        $propertyValue = null;

        $modelAttrbutes = $entry->getAttributes();
        if (is_null($propertyValue) === true && isset($modelAttrbutes[$property]))
        {
            $propertyValue = $entry->{$property};
        }

        return $propertyValue;
    }

    /**
     * Loading a column's value from a model's entry loading a model's relation
     *
     * @param  mixed  $entry
     * @param  String $relationName
     *
     * @return mixed - scalar value or printable object
     */
    protected function loadColumnValueFromEntryRelation($entry, String $relationName)
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
     *
     * @param  mixed $relatedEntry
     * @param  mixed $entry
     *
     * @return String
     */
    protected function getRelationPrintableValue($relatedEntry, $entry) : String
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
     * @param  mixed $entry
     *
     * @return String
     */
    protected function fallbackRelationPritableValue($relatedEntry, $entry) : String
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
