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
     * @var bool
     *
     * Forcing the tag to render content markup
     */
    protected $forceGetContentMarkup = false;

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
     * @return string
     */
    public function __toString() : string
    {
        $templateDirectory = $this->getTemplateDirectory();
        $templateFileName  = $this->getTemplateFileName();

        if (empty($templateFileName) === true && view()->exists($this->getViewFilePath()) === false)
        {
            $templateDirectory = null;
        }

        if (empty($templateDirectory) === true && empty($templateFileName) === true) {
            return $this->getPrintable();
        } else {
            $data = $this->getTempalteData();
            $tagname = camel_case($this->tagName);
            $data[$tagname] = $this;

            return view($this->getViewFilePath(), $data)->render();
        }
    }

    /**
     * Setter for template direcotry property - this is where the templates for the form will be loaded from
     *
     * @param  string $directory
     *
     * @return DomTagInterface
     */
    public function setTemplateDirectory(string $directory) : DomTagInterface
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
     * @return DomTagInterface
     */
    public function setTemplateData(array $data) : DomTagInterface
    {
        $this->templateData = $data;

        return $this;
    }

    /**
     * Returns the data that would be passed to the view
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function getTempalteData(string $key = null) : array
    {
        if ($key !== null)
        {
            return isset($this->templateData[$key])
                ? $this->templateData[$key]
                : null
            ;
        }

        return $this->templateData;
    }

    /**
     * Unsetting data that would be passed to the view
     *
     * @param  string $key
     *
     * @return DomTagInterface
     */
    public function removeTemplateData(string $key) : DomTagInterface
    {
        if (isset($this->getTempalteData[$key]))
        {
            unset($this->getTempalteData[$key]);
        }

        return $this;
    }

    /**
     * Setting a label - template data param, would be passed to the template
     *
     * @param  string $label
     *
     * @return DomTagInterface
     */
    public function setLabel(string $label)
    {
        $this->addTemplateData('label', $label);

        return $this;
    }

    /**
     * Getting the template data param that would be passed to the template as $label variable
     *
     * @param  null
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->getTemplateData('label');
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
     * @return DomTagInterface
     */
    public function addTemplateData(...$params) : DomTagInterface
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
     * @return DomTagInterface
     */
    public function setTemplateFileName(string $path) : DomTagInterface
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
     * @return DomTagInterface
     */
    protected function requireExistingResourcePath(string $directory) : DomTagInterface
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
    protected function requireExistingResourceFile(string $path) : DomTagInterface
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
     * @return string
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
        $content = ($this->isContainerTag || $this->forceGetContentMarkup) === true
            ? $this->getContentMarkup()
            : null
        ;

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
     * @return string
     */
    public function getContentMarkup() : string
    {
        throw new Exception('DomTagInterface::getContentMarkup() needs to be implemented in each extending class!');
    }

    /**
     * Replacing all placeholders of the provided template with their contents
     *
     * @param  string $template
     *
     * @return string
     */
    protected function replaceDomPropertiesPlaceholders(string $template) : string
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
     * @return string
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
     * @return string
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
     * @return string
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
     * @return string
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
     * @return string
     */
    protected function getNonContainerTagTemplatePrototype() : string
    {
        return '<{tagname}{attributes}/>';
    }

    /**
     * Setting an attribute's value for the Dom Tag element
     *
     * @param  string $name
     * @param  mixed $value - scalar value or an array
     *
     * @return DomTagInterface
     */
    public function addAttribute(string $name, $value) : DomTagInterface
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Getting an attribute's value for the Dom Tag element
     *
     * @param  string $name
     * @param  mixed $default - default value
     *
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
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
     * @param  string $name
     *
     * @return mixed
     */
    public function removeAttribute(string $name, $default = null) : DomTagInterface
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
     * @param  string $class
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
     * @param  string $class
     *
     * @return
     */
    public function removeClass(string $class) : DomTagInterface
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
     * @param  string $id
     *
     * @return DomTagInterface
     */
    public function setId(string $id) : DomTagInterface
    {
        $this->addAttribute('id', $id);

        return $this;
    }

    /**
     * Setting id attribute for the Dom Tag element
     *
     * @param  string
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * Generating a unique id for the tag and setting it if there is no id attribute passed
     *
     * @param  null
     *
     * @return DomTagInterface
     */
    public function generateIdIfNotSet() : DomTagInterface
    {
        $hasId = (boolean) $this->getId();

        if ($hasId === true)
        {
            return $this;
        }

        $id = uniqid();
        $this->setId($id);

        return $this;
    }

}
