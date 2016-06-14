<?php

namespace LAdmin\Package\Table;

interface TableSectionInterface
{

    /**
     * Builds the content of the row by passing data in the form of an array
     * The passed data should be a multidimentional array:
     * [
     *     [row-key] => [
     *          [cell-key] => [cell-content],
     *          [cell-key] => [cell-content],
     *          [cell-key] => [cell-content],
     *          [cell-key] => [cell-content],
     *          ...
     *     ],
     *     [row-key] => [
     *          [cell-key] => [cell-content],
     *          [cell-key] => [cell-content],
     *          [cell-key] => [cell-content],
     *          [cell-key] => [cell-content],
     *          ...
     *     ],
     *     ...
     * ]
     *
     * @param  Array $data
     *
     * @return TableSectionInterface
     */
    public function buildContentFromArray(Array $data) : TableSectionInterface;

}
