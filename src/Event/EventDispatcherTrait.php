<?php


namespace Bytes\ResponseBundle\Event;


use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Trait EventDispatcherTrait
 * @package Bytes\ResponseBundle\Event
 */
trait EventDispatcherTrait
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @return $this
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher): self
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }
}