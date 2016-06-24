<?php

namespace LAdmin\Package\FormItem\Instance\BaseFormTags;

use LAdmin\Package\PrintableInterface;

class Email extends Text
{

    /**
     * {@inheritdoc}
     */
    protected $typeAttribute = 'email';

}
