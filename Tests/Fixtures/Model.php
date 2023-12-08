<?php

namespace Bytes\ResponseBundle\Tests\Fixtures;

class Model
{
    /**
     * @var string
     */
    public $foo;

    /**
     * @var string
     */
    private $bar;

    public function getBar()
    {
        return $this->bar;
    }

    public function setBar($bar)
    {
        return $this->bar = $bar;
    }
}
