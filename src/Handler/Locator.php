<?php


namespace Bytes\ResponseBundle\Handler;


use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Class Locator
 * @package Bytes\ResponseBundle\Handler
 */
class Locator
{
    /**
     * Locator constructor.
     * @param ServiceLocator $locator
     */
    public function __construct(protected ServiceLocator $locator)
    {
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->locator->get($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->locator->has($id);
    }
}