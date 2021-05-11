<?php


namespace Bytes\ResponseBundle\HttpClient;


use Bytes\ResponseBundle\Enums\TokenSource;

/**
 * Trait TokenSourceIdentifierTrait
 * @package Bytes\ResponseBundle\HttpClient
 */
trait TokenSourceIdentifierTrait
{
    /**
     * Identifier used for differentiating different token providers
     * @return string|null
     */
    protected static function getIdentifier(): ?string {
        if(property_exists(static::class, 'identifier')) {
            return static::$identifier;
        }
        return null;
    }

    /**
     * Returns the TokenSource for the token
     * @return TokenSource
     */
    abstract protected static function getTokenSource(): TokenSource;
}