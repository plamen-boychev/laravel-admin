<?php

namespace LAdmin\Package\Table\Cell;

use LAdmin\Package\DomTag;

class Cell extends DomTag implements CellInterface
{

    protected $tagName = 'td';
    protected $content = '';

    /**
     * {@inheritdox}
     */
    public function getContentMarkup() : string
    {
        return (String) $this->getContent();
    }

    /**
     * Setting content for the table cell
     * The content needs to be a scalar value, an object implementing the
     * __toString() method, or implementing the LAdmin\Package\PrintableInterface interface
     *
     * @param  mixed $content
     *
     * @return Cell
     */
    public function setContent($content) : Cell
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Content getter for the table cell
     *
     * @param  нулл
     *
     * @return String
     */
    public function getContent()
    {
        return $this->content;
    }

}
