<?php

namespace LAdmin\Package\FormItem\Instance\BaseFormTags;

use LAdmin\Package\FormItem\Instance\SimpleFormItem;
use LAdmin\Package\FormItem\FormItemInterface;
use LAdmin\Package\PrintableInterface;

/**
 * @todo Add method to set option as selected
 */
class Option extends SimpleFormItem
{

    /**
     * {@inheritdoc}
     */
    protected $tagName = 'option';

    /**
     * {@inheritdoc}
     */
    protected $label;

    /**
     * {@inheritdoc}
     */
    public function getContentMarkup() : string
    {
        return $this->label;
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
    public function setValue($value) : FormItemInterface
    {
        if (is_string($value) === false && $value instanceof PrintableInterface)
        {
            throw new Exception("The passed value needs to be either a scalar value or an implementation of PrintableInterface!");
        }

        $this->addAttribute('value', $value);

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
        return $this->getAttribute('value');
    }

    /**
     * Selected setter
     *
     * @param  bool
     *
     * @return FormItemInterface
     */
    public function setSelected(bool $selected) : FormItemInterface
    {
        if ($selected === true) {
            $this->addAttribute('selected', 'selected');
        } else {
            $this->removeAttribute('selected');
        }

        return $this;
    }

    /**
     * Selected getter
     *
     * @param  null
     *
     * @return bool
     */
    public function getSelected() : bool
    {
        return $this->getAttribute('selected', false);
    }

    /**
     * Setting a value for the field, using an attribute
     *
     * @param  string $label
     *
     * @return FormItemInterface
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Getter for the @var $label property
     *
     * @param  null
     *
     * @return string
     */
    public function getLabel() : string
    {
        return $this->label;
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
