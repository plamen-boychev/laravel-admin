<?php

namespace LAdmin\Package\FormItem;

interface FormItemInterface
{

    /**
     * Setting an alias for the form item - a string to reference the item in a form
     *
     * @param  string $alias
     *
     * @return FormItemInterface
     */
    public function setAlias(string $alias) : FormItemInterface;

    /**
     * @var $alias getter - a string to reference the item in a form
     *
     * @param  null
     *
     * @return scalar value
     */
    public function getAlias();

    /**
     * Setting an name for the form item - a string for the form item's name html attribute
     *
     * @param  string $alias
     *
     * @return FormItemInterface
     */
    public function setName(string $name) : FormItemInterface;

    /**
     * @var $name getter - a string for the form item's name html attribute
     *
     * @param  null
     *
     * @return scalar value | null
     */
    public function getName();

}
