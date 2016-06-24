<?php

namespace LAdmin\Package\FormItem\Instance\BaseFormTags;

use LAdmin\Package\PrintableInterface;
use LAdmin\Package\FormItem\FormItemInterface;

/**
 * @todo Checked attribute if is checked
 */
class Checkbox extends Text
{

    /**
     * {@inheritdoc}
     */
    protected $tagName = 'input';

    /**
     * {@inheritdoc}
     */
    protected $typeAttribute = 'checkbox';

    /**
     * {@inheritdoc}
     */
    protected $templateFileName = 'checkbox';

    /**
     * Checked attribute setter
     *
     * @param  bool $checked
     *
     * @return FormItemInterface
     */
    public function setChecked(bool $checked) : FormItemInterface
    {
        if ($checked === true) {
            $this->addAttribute('checked', 'checked');
        } else {
            $this->removeAttribute('checked');
        }

        return $this;
    }

    /**
     * Checked attribute setter
     *
     * @param  null
     *
     * @return bool
     */
    public function getChecked() : bool
    {
        return (bool) $this->getAttribute('checked', false);
    }

}
