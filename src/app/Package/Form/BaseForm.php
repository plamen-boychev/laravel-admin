<?php

namespace LAdmin\Package\Form;

use LAdmin\Package\PrintableInterface;

abstract class BaseForm implements FormInterface, PrintableInterface
{

    /**
     * {@inheritdoc}
     */
    public function __toString() : String
    {
        throw new \Exception('@todo Implement!');
    }

}
