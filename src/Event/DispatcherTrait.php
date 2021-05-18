<?php


namespace Bytes\ResponseBundle\Event;


use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Trait DispatcherTrait
 * @package Bytes\ResponseBundle\Event
 *
 * @property EventDispatcherInterface $dispatcher
 */
trait DispatcherTrait
{
    /**
     * @param StoppableEventInterface $event
     * @param string|null $eventName
     * @return object
     */
    protected function dispatch(StoppableEventInterface $event, string $eventName = null)
    {
        if(empty($eventName)) {
            $eventName = get_class($event);
        }
        return $this->dispatcher->dispatch($event, $eventName);
    }
}