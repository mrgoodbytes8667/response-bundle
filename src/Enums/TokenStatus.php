<?php


namespace Bytes\ResponseBundle\Enums;


use BadMethodCallException;
use Bytes\EnumSerializerBundle\Enums\Enum;
use TypeError;

/**
 * Class TokenStatus
 * @package Bytes\ResponseBundle\Enums
 *
 * @method static self granted()
 * @method static self refreshed()
 * @method static self expired()
 * @method static self revoked()
 */
class TokenStatus extends Enum
{
    /**
     * @param TokenStatus|string $status
     * @return bool
     */
    public static function isActive(TokenStatus|string $status): bool
    {
        if (is_string($status)) {
            try {
                $status = TokenStatus::from($status);
            } catch (BadMethodCallException | TypeError $exception) {
                return false;
            }
        }
        return $status->equals(TokenStatus::granted(), TokenStatus::refreshed());
    }
}
