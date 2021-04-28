<?php


namespace Bytes\ResponseBundle\Services;


/**
 * Interface OAuthPromptInterface
 * @package Bytes\ResponseBundle\Services
 */
interface OAuthPromptInterface
{
    /**
     * Returns the prompt value
     * @return mixed
     */
    public function prompt();
}