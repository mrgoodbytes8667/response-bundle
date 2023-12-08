<?php

namespace Bytes\ResponseBundle\Test;

use Bytes\ResponseBundle\Test\Constraint\AccessTokenInterfaceSame;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;

/**
 * Trait AssertAccessTokenInterfaceTrait.
 */
trait AssertAccessTokenInterfaceTrait
{
    public static function assertTokenEquals(AccessTokenInterface $expected, AccessTokenInterface $actual, string $message = '')
    {
        self::assertThat($actual, AccessTokenInterfaceSame::create($expected), $message);
    }

    public static function assertTokenNotEquals(AccessTokenInterface $expected, AccessTokenInterface $actual, string $message = '')
    {
        self::assertThat($actual, self::logicalNot(AccessTokenInterfaceSame::create($expected)), $message);
    }
}
