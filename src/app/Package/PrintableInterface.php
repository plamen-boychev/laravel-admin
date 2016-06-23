<?php

namespace LAdmin\Package;

interface PrintableInterface
{

    /**
     * Printable (string) value / representation of the object
     *
     * @param  null
     *
     * @return String
     */
    public function __toString() : string;

}
