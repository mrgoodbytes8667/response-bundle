<?php

namespace Bytes\ResponseBundle\EventListener;

use Bytes\ResponseBundle\Event\RevokeTokenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class AbstractRevokeTokenSubscriber.
 */
abstract class AbstractRevokeTokenSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * The code must not depend on runtime state as it will only be called at compile time.
     * All logic depending on runtime state must be put into the individual methods handling the events.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RevokeTokenEvent::class => ['onRevokeToken', 10],
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    abstract public function onRevokeToken(RevokeTokenEvent $event): RevokeTokenEvent;
}
