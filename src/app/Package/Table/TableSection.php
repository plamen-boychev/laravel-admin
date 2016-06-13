<?php

namespace LAdmin\Package\Table;

use LAdmin\Package\DomTag;
use LAdmin\Package\Table\Row\Row;

use LAdmin\Package\Table\Cell\Cell;

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

}
