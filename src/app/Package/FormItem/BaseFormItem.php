<?php

namespace LAdmin\Package\FormItem;

use LAdmin\Package\DomTag;

abstract class BaseFormItem extends DomTag implements FormItemInterface
{

    /**
     * @var string
     *
     * An alias for the form item - a string to reference the item in a form
     */
    protected $alias;

    /**
     * {@inheritdoc}
     */
    public function setAlias(string $alias) : FormItemInterface
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name = null) : FormItemInterface
    {
        $this->addAttribute('name', $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }


    /**
     * Set field as required / not required
     *
     * @param  bool
     *
     * @return FormItemInterface
     */
    public function setRequired(bool $required) : FormItemInterface
    {
        if ($required === true) {
            $this->addAttribute('required', 'required');
        } else {
            $this->removeAttribute('required', 'required');
        }

        return $this;
    }

    /**
     * Returns a flag - is field required
     *
     * @param  null
     *
     * @return bool
     */
    public function getRequired() : bool
    {
        return (bool) $this->getAttribute('required');
    }

}
