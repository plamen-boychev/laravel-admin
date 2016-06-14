<?php

namespace LAdmin\Package\Table;

use LAdmin\Package\DomTag;
use LAdmin\Package\Table\Cell\Cell;
use LAdmin\Package\Table\Cell\HeaderCell;
use LAdmin\Package\Table\Row\Row;
use LAdmin\Package\Table\Head\Head;
use LAdmin\Package\Table\Foot\Foot;
use LAdmin\Package\Table\Body\Body;

abstract class BaseTable extends DomTag implements TableInterface
{

    protected $head;
    protected $foot;
    protected $body;

    protected $tagName = 'table';
    protected $isContainerTag = true;

    /**
     * Constructor
     *
     * @param  null
     *
     * @return TableInterface
     */
    public function __construct()
    {
        parent::__construct();

        $this->head = new Head;
        $this->foot = new Foot;
        $this->body = new Body;
    }

    /**
     * {@inheritdox}
     */
    public function getContentMarkup() : String
    {
        return $this->strigifyTableContents();
    }

    /**
     * Returns the table's inner content as a string
     *
     * @param  null
     *
     * @return String
     */
    public function strigifyTableContents() : String
    {
        return (String) $this->head
            .  (String) $this->body
            .  (String) $this->foot;
    }

    /**
     * Injecting table head object
     *
     * @param  Head
     *
     * @return TableInterface
     */
    public function setHead(Head $head) : TableInterface
    {
        $this->head = $head;

        return $this;
    }

    /**
     * Injecting table foot object
     *
     * @param  Foot
     *
     * @return TableInterface
     */
    public function setFoot(Foot $foot) : TableInterface
    {
        $this->foot = $foot;

        return $this;
    }

    /**
     * Injecting table body object
     *
     * @param  Body
     *
     * @return TableInterface
     */
    public function setBody(Body $body) : TableInterface
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Appending a row to the table's head
     *
     * @param  Row $row
     *
     * @return null
     */
    public function appendHeadRow(Row $row) : TableInterface
    {
        $this->head->appendRow($row);

        return $this;
    }

    /**
     * Prepending a row to the table's head
     *
     * @param  Row $row
     *
     * @return null
     */
    public function prependHeadRow(Row $row) : TableInterface
    {
        $this->head->prependRow($row);

        return $this;
    }

    /**
     * Appending a row to the table's body
     *
     * @param  Row $row
     *
     * @return null
     */
    public function appendBodyRow(Row $row) : TableInterface
    {
        $this->body->appendRow($row);

        return $this;
    }

    /**
     * Prepending a row to the table's body
     *
     * @param  Row $row
     *
     * @return null
     */
    public function prependBodyRow(Row $row) : TableInterface
    {
        $this->body->prependRow($row);

        return $this;
    }

    /**
     * Appending a row to the table's foot
     *
     * @param  Row $row
     *
     * @return null
     */
    public function appendFootRow(Row $row) : TableInterface
    {
        $this->foot->appendRow($row);

        return $this;
    }

    /**
     * Prepending a row to the table's foot
     *
     * @param  Row $row
     *
     * @return null
     */
    public function prependFootRow(Row $row) : TableInterface
    {
        $this->foot->prependRow($row);

        return $this;
    }

    /**
     * Shortcut for setting data to the table's head - passing values for the cells only
     *
     * @param  Array $data
     *
     * @return TableInterface
     */
    public function head(Array $data) : TableInterface
    {
        $this->buildSectionContentFromArray($this->head ,$data);

        return $this;
    }

    /**
     * Shortcut for setting data to the table's body - passing values for the cells only
     *
     * @param  Array $data
     *
     * @return TableInterface
     */
    public function body(Array $data) : TableInterface
    {
        $this->buildSectionContentFromArray($this->body ,$data);

        return $this;
    }

    /**
     * Shortcut for setting data to the table's foot - passing values for the cells only
     *
     * @param  Array $data
     *
     * @return TableInterface
     */
    public function foot(Array $data) : TableInterface
    {
        $this->buildSectionContentFromArray($this->foot ,$data);

        return $this;
    }

    /**
     * Sets content for the section object, content is built using the passed array parameter
     *
     * @param  TableSectionInterface $section
     * @param  Array $data
     *
     * @return
     */
    public function buildSectionContentFromArray(TableSectionInterface $section, Array $data) : TableInterface
    {
        $section->buildContentFromArray($data);

        return $this;
    }

}
