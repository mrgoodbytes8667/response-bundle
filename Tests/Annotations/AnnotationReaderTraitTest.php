<?php

namespace Bytes\ResponseBundle\Tests\Annotations;

use Bytes\ResponseBundle\Annotations\AnnotationReaderTrait;
use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;

class AnnotationReaderTraitTest extends TestCase
{
    public function testSetReader()
    {
        $mock = $this->getMockForTrait(AnnotationReaderTrait::class);
        self::assertNotNull($mock->setReader(new AnnotationReader()));
    }
}
