<?php


namespace Bytes\ResponseBundle\Event;


use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class TokenGrantedEvent
 * Fired after a token is successfully granted.
 * @package Bytes\ResponseBundle\Event
 *
 * @experimental
 */
class TokenGrantedEvent extends AbstractTokenEvent
{
}