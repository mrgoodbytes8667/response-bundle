<?php

namespace Bytes\ResponseBundle\HttpClient;

use Bytes\ResponseBundle\Enums\TokenSource;

trigger_deprecation('mrgoodbytes8667/response-bundle', '2.0.0', 'The TokenSourceIdentifierTrait is deprecated. Use the Client annotation and non-static getIdentifier()/getTokenSource() methods instead.');

/**
 * Trait TokenSourceIdentifierTrait.
 *
 * @deprecated Since 2.0.0. Use the Client annotation and non-static getIdentifier()/getTokenSource() methods instead.
 */
trait TokenSourceIdentifierTrait
{
    /**
     * Identifier used for differentiating different token providers.
     *
     * @deprecated Since 2.0.0. Use the Client annotation and non-static getIdentifier() method instead.
     */
    protected static function getIdentifier(): ?string
    {
        trigger_deprecation('mrgoodbytes8667/response-bundle', '2.0.0', 'Please use the Client annotation and non-static getIdentifier() method instead.');
        if (property_exists(static::class, 'identifier')) {
            return static::$identifier;
        }

        return null;
    }

    /**
     * Returns the TokenSource for the token.
     *
     * @deprecated Since 2.0.0. Use the Client annotation and non-static getTokenSource() method instead.
     */
    abstract protected static function getTokenSource(): TokenSource;
}
