<?php

namespace LAdmin\Package\Table;

use LAdmin\Package\DomTag;
use LAdmin\Package\Table\Row\Row;
use LAdmin\Package\Table\Cell\Cell;
use LAdmin\Package\Table\Cell\HeaderCell;
use LAdmin\Package\Table\Cell\CellInterface;

abstract class TableSection extends DomTag implements TableSectionInterface
{

    protected $rows = [];

    /**
     * {@inheritdox}
     */
    public function getContentMarkup() : String
    {
        return $this->strigifyTableSectionContents();
    }

    /**
     * Returns the table section's inner content as a string
     * Should consist of rows only
     *
     * @param  null
     *
     * @return String
     */
    public function strigifyTableSectionContents() : String
    {
        $rows = $this->rows;

        if (empty($rows))
        {
            return '';
        }

        $markup = implode("\n", array_map(function($row) { return (String) $row; }, $rows));

        return $markup;
    }

    /**
     * Adding a row as at the end
     *
     * @param  Row $row
     *
     * @return TableSectionInterface
     */
    public function appendRow(Row $row) : TableSectionInterface
    {
        array_push($this->rows, $row);

        return $this;
    }

    /**
     * Adding a row as at the beginning
     *
     * @param  Row $row
     *
     * @return TableSectionInterface
     */
    public function prependRow(Row $row) : TableSectionInterface
    {
        array_unshift($this->rows, $row);

        return $this;
    }

    /**
     * {@inheritdox}
     */
    public function buildContentFromArray(Array $data) : TableSectionInterface
    {
        foreach ($data as $rowKey => $rowData)
        {
            $row = new Row;

            foreach ($rowData as $cellIndex => $cellContent)
            {
                $cell = $this->newCellInstance();
                $cell->setContent($cellContent);
                $row->addIndexedCell($cellIndex, $cell);
            }

            $this->addIndexedRow($rowKey, $row);
        }

        return $this;
    }

    /**
     * Returns a newly created instance of the used cell type class
     *
     * @param  null
     *
     * @return CellInterface
     */
    public function newCellInstance() : CellInterface
    {
        return new HeaderCell;
    }

    /**
     * Adds a row with for the specified index, overrides an existing one
     *
     * @param  mixed $index
     * @param  Row $row
     *
     * @return TableSectionInterface
     */
    public function addIndexedRow($index, Row $row) : TableSectionInterface
    {
        $this->rows[$index] = $row;

        return $this;
    }

}
