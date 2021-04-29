<?php


namespace Bytes\ResponseBundle\DependencyInjection;


/**
 * Interface ResponseExtensionInterface
 * @package Bytes\ResponseBundle\DependencyInjection
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