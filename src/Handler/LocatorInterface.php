<?php

namespace Bytes\ResponseBundle\Handler;

/**
 * Interface LocatorInterface.
 */
interface LocatorInterface
{
    /**
     * Return the locator name.
     */
    public static function getDefaultIndexName(): string;
}
