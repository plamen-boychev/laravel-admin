<?php

namespace LAdmin\Package\FormItem;

use LAdmin\Package\PrintableInterface;

abstract class BaseFormItem implements FormItemInterface, PrintableInterface
{

    /**
     * {@inheritdoc}
     */
    public function __toString() : String
    {
        throw new \Exception('@todo Implement!');
    }

}
