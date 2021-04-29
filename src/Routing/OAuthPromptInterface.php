<?php


namespace Bytes\ResponseBundle\Routing;


/**
 * Interface OAuthPromptInterface
 * @package Bytes\ResponseBundle\Routing
 */
interface OAuthPromptInterface
{
    /**
     * Returns the prompt value
     * @return mixed
     */
    public function prompt();
}