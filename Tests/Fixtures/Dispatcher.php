<?php


namespace Bytes\ResponseBundle\Tests\Fixtures;


use Bytes\ResponseBundle\Event\DispatcherTrait;
use Bytes\ResponseBundle\Event\EventDispatcherTrait;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

class Dispatcher
{
    use EventDispatcherTrait, DispatcherTrait;


    /**
     * Dispatcher constructor.
     */
    public function __construct()
    {
        $this->setDispatcher(new EventDispatcher());
    }

    public function triggerFakeEvent(Event $event, string $name = null)
    {
        return $this->dispatch($event, $name);
    }
}
