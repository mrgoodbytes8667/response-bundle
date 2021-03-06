<?php

namespace Bytes\ResponseBundle\Tests\Security\Traits;

use Bytes\ResponseBundle\Security\SecurityTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SecurityTraitTest
 * @package Bytes\ResponseBundle\Tests\Security\Traits
 */
class SecurityTraitTest extends TestCase
{
    /**
     *
     */
    public function testSetTokenUser()
    {
        $trait = $this->getMockForTrait(SecurityTrait::class);
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $this->assertNotNull($trait->setTokenUser($user));
    }

    /**
     *
     */
    public function testSetSecurity()
    {
        $trait = $this->getMockForTrait(SecurityTrait::class);
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $security = new Security($container);
        $this->assertNotNull($trait->setSecurity($security));
    }
}