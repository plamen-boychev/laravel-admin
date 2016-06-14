<?php

namespace LAdmin\Package\Table\Body;

use LAdmin\Package\Table\TableSection;
use LAdmin\Package\Table\Cell\Cell;
use LAdmin\Package\Table\Cell\CellInterface;


class Body extends TableSection
{

    protected $tagName = 'tbody';

    /**
     * {@inheritdoc}
     */
    public function newCellInstance() : CellInterface
    {
        return new Cell;
    }

}
