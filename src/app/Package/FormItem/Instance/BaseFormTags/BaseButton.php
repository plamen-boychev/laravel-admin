<?php

namespace LAdmin\Package\FormItem\Instance\BaseFormTags;

use LAdmin\Package\FormItem\Instance\SimpleFormItem;
use LAdmin\Package\PrintableInterface;

abstract class BaseButton extends SimpleFormItem
{

    /**
     * {@inheritdoc}
     */
    protected $tagName = 'button';

    /**
     * @var string
     *
     * Required!
     * Will be used as a type attribute value
     */
    protected $typeAttribute;

    /**
     * @var mixed
     *
     * Either a scalar value or an implementation of PrintableInterface
     */
    protected $value;

    /**
     * {@inheritdoc}
     */
    public function __toString() : string
    {
        # This is the last moment we're able to set it, as also it's being set
        # every time the tag will be printed. It's done here, instead of the
        # constructor, so it's not wasted on such an insignificant change
        if (empty($this->typeAttribute) === false)
        {
            $this->addAttribute('type', $this->typeAttribute);
        }

        return parent::__toString();
    }

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
     * @return FormItemInterface
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
