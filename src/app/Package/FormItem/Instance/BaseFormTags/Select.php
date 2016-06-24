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
            foreach ($options as $value => $label)
            {
                $option = new Option;
                $option->setValue($value);
                $option->setLabel($label);

                $selectedValue = $this->getValue();
                $optionValue = (string) $value;

                $isSelected = is_array($selectedValue)
                        ? in_array($optionValue, $selectedValue)
                        : ((string) $selectedValue) === ((string) $value)
                ;

                if ($isSelected)
                {
                    $option->setSelected(true);
                }

                $optionInstances[$value] = $option;
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
     * @param  string|array $value
     *
     * @return SimpleFormItem
     *
     * @todo   If value is array -> rise flag for multiple selectable options
     *
     * @throws Exception if the passed value is not printable - either a scalar
     *         value of a PrintableInterface implementation
     */
    public function setValue($value) : SimpleFormItem
    {
        if (is_array($value)) {
            $this->addAttribute('multiple', 'multiple');
        } else {
            $this->removeAttribute('multiple');
        }

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
