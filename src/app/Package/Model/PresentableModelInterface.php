<?php

namespace LAdmin\Package\Model;

interface PresentableModelInterface
{

    /**
     * Provides a list of column names to be displayed
     *
     * @param  null
     *
     * @return Array
     */
    public function getColumns() : Array;

    /**
     * Provides a list of header names to be displayed
     *
     * @param  null
     *
     * @return Array
     */
    public function getHeaders() : Array;

    /**
     * Provides a flag - show / hide table's head
     *
     * @param  null
     *
     * @return bool
     */
    public function showHead() : bool;

    /**
     * Provides a flag - show / hide table's foot
     *
     * @param  null
     *
     * @return bool
     */
    public function showFoot() : bool;

    /**
     * A procedure for modifying the loaded query for the table
     *
     * @param  &$query
     *
     * @return void
     */
    public function tableQueryModifier(&$query);

}
