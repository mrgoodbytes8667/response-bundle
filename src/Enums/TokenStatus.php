<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\StringBackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;
use JetBrains\PhpStorm\Deprecated;
use ValueError;

/**
 * @since 2.0.0
 * @version 5.0.0
 */
enum TokenStatus: string implements StringBackedEnumInterface
{
    use StringBackedEnumTrait;

    case granted = 'granted';
    case refreshed = 'refreshed';
    case expired = 'expired';
    case revoked = 'revoked';

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::granted')]
    public static function granted()
    {
        return static::granted;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::refreshed')]
    public static function refreshed()
    {
        return static::refreshed;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::expired')]
    public static function expired()
    {
        return static::expired;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::revoked')]
    public static function revoked()
    {
        return static::revoked;
    }

    /**
     * @param TokenStatus|string $status
     * @return bool
     */
    public static function isActive(TokenStatus|string $status): bool
    {
        /** @var TokenStatus $enum */
        $enum = null;

        if ($status instanceof TokenStatus) {
            $enum = $status;
        } else {
            try {
                $enum = TokenStatus::from($status);
            } catch (ValueError $exception) {
                return false;
            }
        }
        
        return $enum->equals(TokenStatus::granted, TokenStatus::refreshed);
    }
}
