<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\EasyAdminChoiceEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;
use JetBrains\PhpStorm\Deprecated;
use ValueError;

enum TokenStatus: string implements EasyAdminChoiceEnumInterface
{
    use StringBackedEnumTrait;

    case granted = 'granted';
    case refreshed = 'refreshed';
    case expired = 'expired';
    case revoked = 'revoked';

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function granted(): static
    {
        return static::granted;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function refreshed(): static
    {
        return static::refreshed;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function expired(): static
    {
        return static::expired;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function revoked(): static
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

    /**
     * @return array
     */
    public static function formChoices(): array
    {
        return [
            'Granted' => 'granted',
            'Refreshed' => 'refreshed',
            'Expired' => 'expired',
            'Revoked' => 'revoked',
        ];
    }
}
