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

    /**
     * @var string|null
     *
     * If the directory is set it is used to load templates from files for it's
     * printable value; the structure should be as follows:
     *
     * Template directory path:
     * ./
     *  form.blade.php
     *  fieldset.blade.php
     *  label.blade.php
     *  ./elements
     *      textfield.blade.php
     *      textarea.blade.php
     *      select.blade.php
     *      [form-item-component-alias].blade.php
     *  ./fields
     *      [form-item-alias].blade.php
     *  ./custom
     *      [form-item-alias].blade.php
     *
     * - [form-item-component-alias] - registation alias, used at component registrar
     * - [form-item-alias] - alias of the form item, when added to the form
     */
    protected $templateDirectory;

    /**
     * @var string|null
     *
     * Filename for the tag's template
     */
    protected $templateFileName;

    /**
     * @var array
     *
     * Data to pass to the template
     */
    protected $templateData = [];

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
    public function __toString() : string
    {
        $templateDirectory = $this->getTemplateDirectory();
        $templateFileName  = $this->getTemplateFileName();

        if (empty($templateDirectory) === true && empty($templateFileName) === true) {
            return $this->getPrintable();
        } else {
            $data = $this->getTempalteData();
            $data[$this->tagName] = $this;

            return view($this->getViewFilePath(), $data)->render();
        }
    }

    /**
     * Setter for template direcotry property - this is where the templates for the form will be loaded from
     *
     * @param  string $directory
     *
     * @return PrintableInterface
     */
    public function setTemplateDirectory(string $directory) : PrintableInterface
    {
        $this->requireExistingResourcePath($directory);
        $this->templateDirectory = $directory;

        return $this;
    }

    /**
     * Returns the template direcotry for the tag
     *
     * @param  null
     *
     * @return string|null
     */
    public function getTemplateDirectory()
    {
        return $this->templateDirectory ?? null;
    }

    /**
     * Setting the data to be passed to the template
     *
     * @param  array $data
     *
     * @return PrintableInterface
     */
    public function setTemplateData(array $data) : PrintableInterface
    {
        $this->templateData = $data;

        return $this;
    }

    /**
     * Returns the data that would be passed to the view
     *
     * @param  null
     *
     * @return array
     */
    public function getTempalteData() : array
    {
        return $this->templateData;
    }

    /**
     * Setting the data to be passed to the template
     *
     * @param  array $params - formats:
     *         - [
     *              (string) $key => (mixed) $value,
     *              (string) $key => (mixed) $value,
     *              (string) $key => (mixed) $value,
     *              (string) $key => (mixed) $value,
     *              ...
     *           ] - will be merged to the current
     *         - [
     *              (string) $key,
     *              (mixed) $value
     *           ] - value will be added, indexed by $key
     *
     * @return PrintableInterface
     */
    public function addTemplateData(...$params) : PrintableInterface
    {
        if (empty($params))
        {
            throw new Exception('No data provided for addition to the view variables!');
        }

        $firstParam  = $params[0];

        if (is_array($firstParam) === true && count($params) == 1)
        {
            $this->templateData = $this->templateData + $firstParam;

            return $this;
        }

        $secondParam = $params[1];

        if (count($params) == 2 || is_string($firstParam) === true)
        {
            $this->templateData[$firstParam] = $secondParam;

            return $this;
        }

        throw new Exception('Unsupported parameters format!');
    }

    /**
     * Checks if the passed array is associative or sequential
     *
     * @param  array $array
     *
     * @return bool
     */
    public function isAssociativeArray(array $array) : bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Setter for template file property - a template file for the tag
     *
     * @param  string $path
     *
     * @return PrintableInterface
     */
    public function setTemplateFileName(string $path) : PrintableInterface
    {
        $this->templateFileName = $path;

        return $this;
    }

    /**
     * Returns the template file name for the tag
     *
     * @param  null
     *
     * @return string|null
     */
    public function getTemplateFileName()
    {
        return $this->templateFileName ?? null;
    }

    /**
     * Returns the full path to the template for the form, using the specified
     * form skin / directory
     *
     * @param  null
     *
     * @return string
     */
    public function getViewFilePath() : string
    {
        return $this->getTemplateDirectory() . '.' . $this->getTemplateName();
    }

    /**
     * Returns the name of the template for the tag
     *
     * @param  null
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return $this->getTemplateFileName() ?? $this->tagName;
    }

    /**
     * Verifies the specified view directory exists
     *
     * @param  string $directory
     *
     * @return PrintableInterface
     */
    protected function requireExistingResourcePath(string $directory) : PrintableInterface
    {
        $fullPath = $this->existsInViews($directory);

        if (empty($fullPath))
        {
            throw new Exception("Path [{$fullPath}] does not exist!");
        }

        return $this;
    }

    /**
     * Requires the specified file path to be an existing one
     *
     * @param  string $path
     *
     * @return  === true
     */
    protected function requireExistingResourceFile(string $path) : PrintableInterface
    {
        $phpFilePath   = $path . '.php';
        $bladeFilePath = $path . '.blade.php';

        $existsPhpFilePath = $this->existsInViews($path);
        $existsBladeFilePath = $this->existsInViews($path);

        if (empty($existsPhpFilePath) === true && empty($existsBladeFilePath) === true)
        {
            throw new Exception("View [{$path}] does not exist!");
        }

        return $this;
    }

    /**
     * Returns a flag - does the passed directory or file exist in the views directory
     *
     * @param  string $directory
     *
     * @return bool
     */
    protected function existsInViews(string $directory) : bool
    {
        $ds = DIRECTORY_SEPARATOR;
        $path = base_path();
        $fullPath = $path . $ds . 'resources' . $ds . 'views' . $ds . implode($ds, explode('.', $directory));
        $fullPath = realpath($fullPath);

        return (bool) $fullPath;
    }

    /**
     * Returns a string representing the object instance as a string
     *
     * @param  null
     *
     * @return String
     */
    public function getPrintable() : string
    {
        $template = $this->baseTagTemplate;

        if (empty($template))
        {
            throw new Exception('No template was set! You need to either pass value for it or specify the tag\'s name!');
        }

        $attributes = $this->replaceDomPropertiesPlaceholders($template);
        $attributes = empty($attributes) === true
            ? ''
            : " {$attributes}"
        ;

        $markup  = implode($attributes, explode('{attributes}', $template));
        $content = $this->isContainerTag === true ? $this->getContentMarkup() : null;

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
    public function getContentMarkup() : string
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
    protected function replaceDomPropertiesPlaceholders(String $template) : string
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
    protected function scalarizeTagAttribute($value) : string
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
    protected function scalarizeAttributeValues($value) : string
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
    protected function scalarizeNonScalar($value) : string
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
    protected function getContainerTagTemplatePrototype() : string
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
    protected function getNonContainerTagTemplatePrototype() : string
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
     * Getting an attribute's value for the Dom Tag element
     *
     * @param  String $name
     * @param  mixed $default - default value
     *
     * @return mixed
     */
    public function getAttribute(String $name, $default = null)
    {
        if (isset($this->attributes[$name]) === false)
        {
            return $default;
        }

        return $this->attributes[$name];
    }

    /**
     * Removing an attribute's value for the Dom Tag element
     *
     * @param  String $name
     *
     * @return mixed
     */
    public function removeAttribute(String $name, $default = null) : DomTagInterface
    {
        if (isset($this->attributes[$name]))
        {
            unset($this->attributes[$name]);
        }

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
