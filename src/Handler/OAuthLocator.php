<?php


namespace Bytes\ResponseBundle\Handler;


use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Class OAuthLocator
 * @package Bytes\ResponseBundle\Handler
 */
class OAuthLocator
{
    /**
     * OAuthLocator constructor.
     * @param ServiceLocator $locator
     */
    public function __construct(protected ServiceLocator $locator)
    {
    }
}