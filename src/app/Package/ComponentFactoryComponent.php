<?php

namespace LAdmin\Package;

abstract class ComponentFactoryComponent implements ComponentFactoryComponentInterface
{

    /**
     * @var Array $registeredTypes
     */
    protected $registeredTypes = [];

    /**
     * Registering a type of table
     *
     * @param  String $alias
     * @param  String $class
     *
     * @return ComponentFactoryComponentInterface
     */
    public function register(String $alias, String $class) : ComponentFactoryComponentInterface
    {
        $this->addRegisteredTypes($alias, $class);

        return $this;
    }

    /**
     * Adds a table type to the registered ones
     *
     * @param  String $alias
     * @param  String $class
     *
     * @return ComponentFactoryComponentInterface
     */
    public function addRegisteredTypes(String $alias, String $class) : ComponentFactoryComponentInterface
    {
        $this->registeredTypes[$alias] = $class;

        return $this;
    }

    /**
     * Instantiates a table matched by alias
     *
     * @param  String $alias
     *
     * @return TableInterface
     */
    public function factory(String $alias)
    {
        return $this->instatiateRegiteredType($alias);
    }

    /**
     * Creating an instance of the specified type, matched by alias
     *
     * @param  String $alias
     *
     * @return mixed
     */
    protected function instatiateRegiteredType($alias)
    {
        $className = $this->registeredTypes[$alias] ?? null;

        if (is_null($className) === true)
        {
            throw new Exception("FormType \"{$alias}\" was never registered!");
        }

        if (class_exists($className) === false)
        {
            throw new Exception("Class \"{$className}\" for component type type \"{$alias}\" was never registered!");
        }

        $className = '\\' . $className;

        return new $className;
    }

}
