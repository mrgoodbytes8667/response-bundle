<?php

namespace Bytes\ResponseBundle\Tests\Fixtures\Request;

use Bytes\ResponseBundle\Request\AbstractSignature;
use Bytes\ResponseBundle\Request\SignatureInterface;
use Symfony\Component\HttpFoundation\HeaderBag;

class EventSubSignature extends AbstractSignature implements SignatureInterface
{
    /**
     * @var string
     */
    public const SIGNATURE_FIELD = 'twitch-eventsub-message-signature';

    /**
     * @var string
     */
    public const TWITCH_EVENTSUB_MESSAGE_ID = 'twitch-eventsub-message-id';

    /**
     * @var string
     */
    public const TWITCH_EVENTSUB_MESSAGE_TIMESTAMP = 'twitch-eventsub-message-timestamp';

    public static function getSignatureField(): string
    {
        return static::SIGNATURE_FIELD;
    }

    /**
     * Return the locator name.
     */
    public static function getDefaultIndexName(): string
    {
        return 'EVENTSUB';
    }

    protected function getHashString(HeaderBag $headers, bool|string|null $content): string
    {
        return $headers->get(self::TWITCH_EVENTSUB_MESSAGE_ID).$headers->get(self::TWITCH_EVENTSUB_MESSAGE_TIMESTAMP).$content;
    }
}
