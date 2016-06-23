<?php

namespace LAdmin\Sandbox\Iteration;

trait Iterable
{

    protected $iterableElements = [];

    // public function __construct(array $iterableElements = array())
    // {
    //     foreach ($iterableElements as $value) {
    //         $this->attach(new \SplFileInfo($value));
    //     }
    //     $this->rewind();
    // }

    public function attach($newElement)
    {
        $this->iterableElements[] = $newElement;
    }

    public function rewind()
    {
        reset($this->iterableElements);
    }

    public function valid()
    {
        return false !== $this->current();
    }

    public function next()
    {
        next($this->iterableElements);
    }

    public function current()
    {
        return current($this->iterableElements);
    }

    public function key()
    {
        return key($this->iterableElements);
    }

}
