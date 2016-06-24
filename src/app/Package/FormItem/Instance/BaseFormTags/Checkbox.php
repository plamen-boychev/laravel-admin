<?php

namespace LAdmin\Package\FormItem\Instance\BaseFormTags;

use LAdmin\Package\PrintableInterface;

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

}
