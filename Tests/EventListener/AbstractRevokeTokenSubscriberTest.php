<?php

namespace Bytes\ResponseBundle\Tests\EventListener;

use Bytes\ResponseBundle\EventListener\AbstractRevokeTokenSubscriber;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractRevokeTokenSubscriberTest.
 */
class AbstractRevokeTokenSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents()
    {
        $subscriber = $this->getMockForAbstractClass(AbstractRevokeTokenSubscriber::class);

        self::assertCount(1, $subscriber->getSubscribedEvents());
    }
}
