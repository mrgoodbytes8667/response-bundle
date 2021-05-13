<?php

namespace Bytes\ResponseBundle\Tests\Annotations;

use Bytes\ResponseBundle\Annotations\AnnotationReaderTrait;
use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;

/**
 * Class AnnotationReaderTraitTest
 * @package Bytes\ResponseBundle\Tests\Annotations
 */
class AnnotationReaderTraitTest extends TestCase
{
    /**
     *
     */
    public function testSetReader()
    {
        $mock = $this->getMockForTrait(AnnotationReaderTrait::class);

        $this->assertNotNull($mock->setReader(new AnnotationReader()));
    }
}
