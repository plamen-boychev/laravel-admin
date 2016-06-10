<?php

namespace LAdmin\Package;

interface PrintableInterface
{

    /**
     * Printable (string) value / representation of the object
     *
     * @param  null
     *
     * @todo   Describe the method
     *
     * @return String
     */
    public function __toString() : String;

}
