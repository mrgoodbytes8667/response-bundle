<?php

namespace Bytes\ResponseBundle\Tests\EventListener;

use Bytes\ResponseBundle\EventListener\AbstractRevokeTokenSubscriber;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractRevokeTokenSubscriberTest
 * @package Bytes\ResponseBundle\Tests\EventListener
 */
class AbstractRevokeTokenSubscriberTest extends TestCase
{
    /**
     *
     */
    public function testGetSubscribedEvents()
    {
        $subscriber = $this->getMockForAbstractClass(AbstractRevokeTokenSubscriber::class);

        $this->assertCount(1, $subscriber->getSubscribedEvents());
    }
}