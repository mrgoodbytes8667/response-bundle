<?php

namespace Bytes\ResponseBundle\DependencyInjection;

/**
 * Interface ResponseExtensionInterface.
 */
interface ResponseExtensionInterface
{
    /**
     * @return string[]
     */
    public static function getEndpoints(): array;

    /**
     * @return string[]
     */
    public static function getAddRemoveParents(): array;
}
