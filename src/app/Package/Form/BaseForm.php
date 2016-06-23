<?php

namespace LAdmin\Package\Form;

use Exception;
use LAdmin\Package\DomTag;
use LAdmin\Package\FormItem\FormItemInterface;

abstract class BaseForm extends DomTag implements FormInterface
{

    /**
     * {@inheritdoc}
     */
    protected $tagName = 'form';

    /**
     * @var array
     *
     * A collection of form items for the form
     *
     * @todo Allow nested forms as form items
     */
    protected $formItems = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getContentMarkup() : string
    {
        return $this->strigifyChildren();
    }

    /**
     * Setting method
     *
     * @param  string $method
     *
     * @return FormInterface
     */
    public function setMethod(string $method) : FormInterface
    {
        $this->addAttribute('method', $method);

        return $this;
    }

    /**
     * Setting action
     *
     * @param  string $action
     *
     * @return FormInterface
     */
    public function setAction(string $action) : FormInterface
    {
        $this->addAttribute('action', $action);

        return $this;
    }

    /**
     * Adding a form item
     *
     * @param  FormItemInterface $item
     *
     * @return FormInterface
     */
    public function addFormItem(FormItemInterface $item) : FormInterface
    {
        $alias = $item->getAlias() ?? $this->autoFormItemAlias();
        $this->formItems[$alias] = $item;

        return $this;
    }

    /**
     * Generates an alias for the passed form item instance
     *
     * @param  FormItemInterface $item
     *
     * @return string|int
     */
    protected function autoFormItemAlias()
    {
        return sizeof($this->formItems);
    }

    /**
     * Returns a strig representation of child elements
     *
     * @param  null
     *
     * @return string
     */
    protected function strigifyChildren() : string
    {
        $childrenAsString = [];
        $iterable = $this->formItems;
        $templateDirectory = $this->getTemplateDirectory() ?? null;

        foreach ($iterable as $key => $item)
        {
            if (is_null($templateDirectory) === false)
            {
                $item->setTemplateDirectory($templateDirectory);
            }

            $childrenAsString[$key] = (string) $item;
        }

        $childrenAsString = implode('', $childrenAsString);

        return $childrenAsString;
    }

    /**
     * Returns all elements representing the object as iterable element
     *
     * @param  null
     *
     * @return array
     */
    public function getIterableElements() : array
    {
        $elements = [];

        array_merge($elements, $this->formItems);

        return $elements;
    }

}
