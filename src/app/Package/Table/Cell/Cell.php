<?php

namespace LAdmin\Package\Table\Cell;

use LAdmin\Package\DomTag;

class Cell extends DomTag
{

    protected $tagName = 'td';
    protected $content = '';

    /**
     * {@inheritdox}
     */
    public function getContentMarkup() : String
    {
        return $this->getContent();
    }

    /**
     * Setting content for the table cell
     *
     * @param  String $content
     *
     * @return Cell
     */
    public function setContent(String $content) : Cell
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Setting content for the table cell
     *
     * @param  String $content
     *
     * @return String
     */
    public function getContent() : String
    {
        return $this->content;
    }

}
