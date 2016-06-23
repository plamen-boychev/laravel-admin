<?php

namespace LAdmin\Package\ListView;

use LAdmin\Package\PrintableInterface;

abstract class BaseListView implements ListViewInterface, PrintableInterface
{

    /**
     * {@inheritdoc}
     */
    public function __toString() : string
    {
        throw new \Exception('@todo Implement!');
    }

}
