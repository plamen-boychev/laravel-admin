<?php

namespace LAdmin\Package\FormItem\Instance\BaseFormTags;

use LAdmin\Package\FormItem\Instance\SimpleFormItem;
use LAdmin\Package\PrintableInterface;

class Textarea extends SimpleFormItem
{

    /**
     * {@inheritdoc}
     */
    protected $tagName = 'textarea';

    /**
     * @var mixed
     *
     * Either a scalar value or an implementation of PrintableInterface
     */
    protected $value;

    /**
     * {@inheritdoc}
     */
    public function getContentMarkup() : string
    {
        return $this->getValue();
    }

    /**
     * Setting a value for the field, using an attribute
     *
     * @param  string|PrintableInterface $value
     *
     * @return SimpleFormItem
     *
     * @throws Exception if the passed value is not printable - either a scalar
     *         value of a PrintableInterface implementation
     */
    public function setValue($value) : SimpleFormItem
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Getter for the @var $value property
     *
     * @param  null
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns a string representation of the object
     *
     * @param  null
     *
     * @return string
     */
    public function render() : string
    {
        return $this->__toString();
    }

}
