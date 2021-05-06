<?php


namespace Bytes\ResponseBundle\Event;


use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class TokenRevokedEvent
 * Fired after a token is successfully revoked.
 * @package Bytes\ResponseBundle\Event
 *
 * @experimental
 */
class TokenRevokedEvent extends AbstractTokenEvent
{

}