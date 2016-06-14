<?php

namespace LAdmin\Package\Table\Row;

use LAdmin\Package\DomTag;
use LAdmin\Package\Table\Cell\Cell;

class Row extends DomTag
{

    protected $tagName = 'tr';
    protected $cells = [];
    protected $printIfEmpty = false;

    /**
     * {@inheritdox}
     */
    public function getContentMarkup() : String
    {
        return $this->strigifyRowContents();
    }

    /**
     * Returns the row's inner content as a string
     * Should consist of cells only
     *
     * @param  null
     *
     * @return String
     */
    public function strigifyRowContents() : String
    {
        $cells = $this->cells;

        if (empty($cells))
        {
            return '';
        }

        $cellsMarkup = array_map(function($cell) { return (String) $cell; }, $cells);
        $markup = implode("\n", $cellsMarkup);

        return $markup;
    }

    /**
     * Adding a row as at the end
     *
     * @param  Cell $cell
     *
     * @return Cell
     */
    public function appendCell(Cell $cell) : Row
    {
        array_push($this->cells, $cell);

        return $this;
    }

    /**
     * Adding a row as at the beginning
     *
     * @param  Cell $cell
     *
     * @return Cell
     */
    public function prependCell(Cell $cell) : Row
    {
        array_unshift($this->cells, $cell);

        return $this;
    }

    /**
     * Adds a row with for the specified index, overrides an existing one
     *
     * @param  mixed $index
     * @param  Cell $cell
     *
     * @return TableSectionInterface
     */
    public function addIndexedCell($index, Cell $cell) : Row
    {
        $this->cells[$index] = $cell;

        return $this;
    }

    /**
     * Setting the content of the row by passing an array of the cell indexes / contents
     *
     * @param  Array $data
     *
     * @return Row
     */
    public function setContentFromArray(Array $data) : Row
    {
        foreach ($data as $index => $value)
        {
            $cell = new Cell;
            $cell->setContent($value);
            $this->addIndexedCell($index, $cell);
        }

        return $this;
    }

}
