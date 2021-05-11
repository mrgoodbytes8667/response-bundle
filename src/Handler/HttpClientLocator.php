<?php


namespace Bytes\ResponseBundle\Handler;


use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Class HttpClientLocator
 * @package Bytes\ResponseBundle\Handler
 */
class HttpClientLocator
{
    /**
     * HttpClientLocator constructor.
     * @param ServiceLocator $locator
     */
    public function __construct(protected ServiceLocator $locator)
    {
    }
}