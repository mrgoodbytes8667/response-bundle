<?php


namespace Bytes\ResponseBundle\Handler;


/**
 * Interface LocatorInterface
 * @package Bytes\ResponseBundle\Handler
 */
interface LocatorInterface
{
    /**
     * Return the locator name
     * @return string
     */
    public static function getDefaultIndexName(): string;
}