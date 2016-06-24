<?php

namespace LAdmin\Package\FormItem\Instance\BaseFormTags;

use LAdmin\Package\FormItem\Instance\SimpleFormItem;
use LAdmin\Package\PrintableInterface;

class Select extends SimpleFormItem
{

    /**
     * {@inheritdoc}
     */
    protected $tagName = 'select';

    /**
     * @var mixed
     *
     * Either a scalar value or an implementation of PrintableInterface
     */
    protected $value;

    /**
     * @var Array
     *
     * Associative array of value => label pairs
     */
    protected $options;

    /**
     * {@inheritdoc}
     */
    public function getContentMarkup() : string
    {
        return $this->stringifyOptions();
    }

    /**
     * Returns a string representation of the oprions array
     *
     * @param  null
     *
     * @return string
     */
    public function stringifyOptions() : string
    {
        $options = [];

        foreach ($this->options as $key => $value)
        {
            $options[$key] = (string) $value;
        }

        $options = implode('', $options);

        return $options;
    }

    /**
     * Options setter
     *
     * @param  array $options
     *
     * @return Select
     */
    public function setOptions(array $options)
    {
        $currentOption = current($options);

        if ($currentOption instanceof Option) {
            $this->options = $options;
        } else {
            $optionInstances = [];
            foreach ($options as $key => $value)
            {
                $option = new Option;
                $option->setValue($key);
                $option->setLabel($value);

                $optionInstances[$key] = $option;
            }
            $this->options = $optionInstances;
        }

        return $this;
    }

    /**
     * Options getter
     *
     * @param  null
     *
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
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
