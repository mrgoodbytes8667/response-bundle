<?php

namespace Bytes\ResponseBundle\Tests\Annotations;

use Bytes\ResponseBundle\Annotations\AnnotationReaderTrait;
use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;

/**
 * @deprecated Annotation support will be dropped in v6.0.0
 */
class AnnotationReaderTraitTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @group legacy
     */
    public function testSetReader()
    {
        $mock = $this->getMockForTrait(AnnotationReaderTrait::class);
        $this->expectDeprecation('Since mrgoodbytes8667/response-bundle 5.4.0: Annotation support will be dropped in v6.0.0.');
        self::assertNotNull($mock->setReader(new AnnotationReader()));
    }
}
