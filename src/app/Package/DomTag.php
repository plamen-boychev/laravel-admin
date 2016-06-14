<?php

namespace LAdmin\Package;

use Exception;

abstract class DomTag implements DomTagInterface, PrintableInterface
{

    /**
     * @var string
     *
     * A template (html) representing the DOM tag
     */
    protected $baseTagTemplate = null;

    /**
     * @var string
     *
     * The tag's name
     * Will help building a default template if such is not specified
     */
    protected $tagName;

    /**
     * @var bool
     *
     * Whether or not to print any markup if there is no inner markup
     */
    protected $printIfEmpty = true;

    /**
     * @var Array
     *
     * Contains all attributes of the tag.
     * Will be used to build the markup for the tag.
     */
    protected $attributes = [];

    /**
     * @var bool
     *
     * A flag for whether or not the tag is a container one
     * Will help building a default template if such is not specified
     */
    protected $isContainerTag = true;

    public function __construct()
    {
        $this->buildDomTemplate();
    }

    /**
     * Printing the tag
     *
     * @param  null
     *
     * @return String
     */
    public function __toString() : String
    {
        return $this->getPrintable();
    }

    /**
     * Returns a string representing the object instance as a string
     *
     * @param  null
     *
     * @return String
     */
    public function getPrintable() : String
    {
        $template = $this->baseTagTemplate;

        if (empty($template))
        {
            throw new Exception('No template was set! You need to either pass value for it or specify the tag\'s ');
        }

        $attributes = $this->replaceDomPropertiesPlaceholders($template);
        $attributes = empty($attributes) === true
            ? ''
            : " {$attributes}"
        ;

        $markup  = implode($attributes, explode('{attributes}', $template));

        $content = $this->getContentMarkup();

        if (empty($content) === true && $this->shouldPrintIfNoContentMarkup() === false )
        {
            return '';
        }

        $markup  = implode($content, explode('{content}', $markup));

        return $markup;
    }

    /**
     * Returns a flag - whether or not to print any markup if there is no inner markup
     *
     * @param  null
     *
     * @return bool
     */
    public function shouldPrintIfNoContentMarkup() : bool
    {
        return is_bool($this->printIfEmpty)
            ? $this->printIfEmpty
            : false
        ;
    }

    /**
     * Returns the tag's content as a string
     *
     * @param  null
     *
     * @return String
     */
    public function getContentMarkup() : String
    {
        throw new Exception('DomTagInterface::getContentMarkup() needs to be implemented in each extending class!');
    }

    /**
     * Replacing all placeholders of the provided template with their contents
     *
     * @param  String $template
     *
     * @return String
     */
    protected function replaceDomPropertiesPlaceholders(String $template) : String
    {
        $attrData = $this->attributes;
        $attributes = [];

        foreach ($attrData as $attribute => $value)
        {
            $value = is_scalar($value)
                ? $value
                : $this->scalarizeAttributeValues($value)
            ;
            array_push($attributes, "{$attribute}=\"{$value}\"");
        }

        $attributes = $this->scalarizeTagAttribute($attributes);

        return $attributes;
    }

    /**
     * Returns the passed non- scalar value as a string - combning all attributes
     * of the tag
     *
     * @param  mixed $value
     *
     * @return String
     */
    protected function scalarizeTagAttribute($value) : String
    {
        return $this->scalarizeNonScalar($value);
    }

    /**
     * Returns the passed non- scalar value as a string - combining all values
     * of a registered attribute
     *
     * @param  mixed $value
     *
     * @return String
     */
    protected function scalarizeAttributeValues($value) : String
    {
        return $this->scalarizeNonScalar($value);
    }

    /**
     * Scalarizing a non-scalar value
     *
     * @todo   Implement a mechanism for choosing the way the attribute values
     *         would be combined - separator / combining method, suffix, prefix etc.
     *
     * @param  mixed $value
     *
     * @return String
     */
    protected function scalarizeNonScalar($value) : String
    {
        if (is_array($value) === true) {
            return implode(' ', $value);
        }

        $type = gettype($value);

        throw new Exception("Value of type {$type} is not supported as an attribute!");
    }

    /**
     * Builds a template for the tag if such is not specified
     *
     * @param  null
     *
     * @return DomTag
     */
    protected function buildDomTemplate() : DomTagInterface
    {
        if (is_null($this->baseTagTemplate) === false && is_string($this->baseTagTemplate) === true)
        {
            return $this;
        }

        if (empty($this->tagName))
        {
            throw new Exception('@var DomTagInterface::$tagName is required for LAdmin\\Package\\DomTag class instance!');
        }

        $prototype = $this->isContainerTag
            ? $this->getContainerTagTemplatePrototype()
            : $this->getNonContainerTagTemplatePrototype()
        ;

        $template = implode($this->tagName, explode('{tagname}', $prototype));
        $this->baseTagTemplate = $template;

        return $this;
    }

    /**
     * Returns a default prototype for container tag
     *
     * @param  null
     *
     * @return String
     */
    protected function getContainerTagTemplatePrototype() : String
    {
        return '<{tagname}{attributes}>{content}</{tagname}>';
    }

    /**
     * Returns a default prototype for non container tag
     *
     * @param  null
     *
     * @return String
     */
    protected function getNonContainerTagTemplatePrototype() : String
    {
        return '<{tagname}{attributes}/>';
    }

    /**
     * Setting an attribute's value for the Dom Tag element
     *
     * @param  String $name
     * @param  mixed $value - scalar value or an array
     *
     * @return DomTagInterface
     */
    public function addAttribute(String $name, $value) : DomTagInterface
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Adding a class to the Dom Tag element
     *
     * @param  String $class
     *
     * @return DomTagInterface
     */
    public function addClass($class) : DomTagInterface
    {
        if (!isset($this->attributes['class']))
        {
            $this->attributes['class'] = [];
        }

        array_push($this->attributes['class'], $class);

        return $this;
    }

    /**
     * Adding multiple classes to the Dom Tag element
     *
     * @param  Array $classes
     *
     * @return DomTagInterface
     */
    public function addClasses(Array $classes) : DomTagInterface
    {
        foreach ($classes as $key => $value)
        {
            $this->addClass($value);
        }

        return $this;
    }

    /**
     * Removes a class from the set ones if such exists
     *
     * @param  String $class
     *
     * @return
     */
    public function removeClass(String $class) : DomTagInterface
    {
        if (isset($this->attributes['class']) === false)
        {
            return $this;
        }

        $classPosition = array_search($class, $this->attributes['class']);

        if ($classPosition === false)
        {
            return $this;
        }

        unset($this->attributes['class'][$classPosition]);

        return $this;
    }

    /**
     * Setting id attribute for the Dom Tag element
     *
     * @param  String $id
     *
     * @return DomTagInterface
     */
    public function setId(String $id) : DomTagInterface
    {
        $this->attributes['id'] = $id;

        return $this;
    }

}
