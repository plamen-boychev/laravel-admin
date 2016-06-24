<?php

namespace LAdmin\Package\FormItem\Instance\BaseFormTags;

use LAdmin\Package\FormItem\Instance\SimpleFormItem;
use LAdmin\Package\PrintableInterface;

class SubmitButton extends BaseButton
{

    /**
     * {@inheritdoc}
     */
    protected $typeAttribute = 'submit';

}
