<?php

namespace LAdmin\Package\Table;

use LAdmin\Package\PrintableInterface;

abstract class BaseTable implements TableInterface, PrintableInterface
{

    /**
     * {@inheritdoc}
     */
    public function __toString() : String
    {
        throw new \Exception('@todo Implement!');
    }

}
