<?php

namespace Bytes\ResponseBundle\Handler;

use Symfony\Component\DependencyInjection\ServiceLocator;

class Locator
{
    public function __construct(protected ServiceLocator $locator)
    {
    }

    public function get($id)
    {
        return $this->locator->get($id);
    }

    public function has(string $id): bool
    {
        return $this->locator->has($id);
    }
}
