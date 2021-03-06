<?php

namespace LAdmin\Package\FormItem\Instance\BaseFormTags;

use LAdmin\Package\FormItem\Instance\SimpleFormItem;
use LAdmin\Package\PrintableInterface;

class Text extends SimpleFormItem
{

    /**
     * {@inheritdoc}
     */
    protected $tagName = 'input';

    /**
     * @var string
     *
     * Required!
     * Will be used as a type attribute value
     */
    protected $typeAttribute = 'text';

    /**
     * {@inheritdoc}
     */
    protected $isContainerTag = false;

    /**
     * {@inheritdoc}
     */
    public function __toString() : string
    {
        # This is the last moment we're able to set it, as also it's being set
        # every time the tag will be printed. It's done here, instead of the
        # constructor, so it's not wasted on such an insignificant change
        $this->addAttribute('type', $this->typeAttribute);

        return parent::__toString();
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
     * Setting a placeholder - template data param, would be passed to the template
     *
     * @param  string $placeholder
     *
     * @return DomTagInterface
     */
    public function setPlaceholder(string $placeholder)
    {
        $this->addAttribute('placeholder', $placeholder);

        return $this;
    }

    /**
     * Getting the template data param that would be passed to the template as $placeholder variable
     *
     * @param  null
     *
     * @return string|null
     */
    public function getPlaceholder(string $placeholder)
    {
        return $this->getAttribute('placeholder');
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
