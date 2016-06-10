<?php

namespace LAdmin\Package\ListColumn;

use LAdmin\Package\PrintableInterface;

abstract class BaseListColumn implements ListColumnInterface, PrintableInterface
{

    /**
     * {@inheritdoc}
     */
    public function __toString() : String
    {
        throw new \Exception('@todo Implement!');
    }

}
