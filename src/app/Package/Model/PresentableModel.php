<?php

namespace LAdmin\Package\Model;

use Exception;

trait PresentableModel
{

    /**
     * @var Array
     *
     * Columns to list - properties of the model
     */
    // protected $columns = [];

    /**
     * @var Array
     *
     * Headers of the columns
     */
    // protected $headers = [];

    /**
     * Makes sure all the minimal required configuration needed for the
     * model is set:
     *  - columns
     *  - headers
     *
     * @throws Exception if the minimal configuration is not covered
     *
     * @param  null
     *
     * @return void
     */
    public function requireMinimalPresentableModelConfiguration()
    {
        $this->requirePresentableModelColumnsConfiguration();
        $this->requirePresentableModelHeadersConfiguration();
    }

    /**
     * Required the minimal configuration is met for model columns
     *
     * @throws Exception if the minimal configuration requirements are not met
     *
     * @param  null
     *
     * @return null
     */
    protected function requirePresentableModelColumnsConfiguration()
    {
        if (empty($this->columns) === true)
        {
            $this->indicateUnmetConfigurationRequirements('Minimal required configuration for PresentableModel::$columns is not met!');
        }
    }

    /**
     * Required the minimal configuration is met for model headers
     *
     * @throws Exception if the minimal configuration requirements are not met
     *
     * @param  null
     *
     * @return null
     */
    protected function requirePresentableModelHeadersConfiguration()
    {
        if (empty($this->headers) === true)
        {
            $this->indicateUnmetConfigurationRequirements('Minimal required configuration for PresentableModel::$headers is not met!');
        }
    }

    /**
     * Throws the passed message as an error
     *
     * @param  String $message
     *
     * @return void
     *
     * @throws Exception
     */
    protected function indicateUnmetConfigurationRequirements(String $message)
    {
        throw new Exception($message);
    }

    /*
     * {@inheritdoc}
     */
    public function showHead() : bool
    {
        return true;
    }

    /*
     * {@inheritdoc}
     */
    public function showFoot() : bool
    {
        return true;
    }

    /*
     * {@inheritdoc}
     */
    public function getColumns() : array
    {
        if (empty($this->columns))
        {
            throw new Exception('Column names not specified! Either override '
                . 'this method, or set the @var $columns property suitable value!');
        }

        return $this->columns;
    }

    /*
     * {@inheritdoc}
     */
    public function getHeaders() : array
    {
        if (empty($this->headers))
        {
            throw new Exception('Header names not specified! Either override '
                . 'this method, or set the @var $headers property suitable value!');
        }

        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function tableQueryModifier(&$query)
    {
        return;
    }

}
