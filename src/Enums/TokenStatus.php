<?php

namespace Bytes\ResponseBundle\Enums;

use Bytes\EnumSerializerBundle\Enums\StringBackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;
use ValueError;

/**
 * @since 2.0.0
 *
 * @version 7.0.0
 */
enum TokenStatus: string implements StringBackedEnumInterface
{
    use StringBackedEnumTrait;

    case granted = 'granted';
    case refreshed = 'refreshed';
    case expired = 'expired';
    case revoked = 'revoked';

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
