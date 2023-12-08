<?php

namespace Bytes\ResponseBundle\Test;

use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\HttpClient\AbstractClient;
use Bytes\ResponseBundle\Interfaces\ClientTokenResponseInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;

/**
 * Trait AssertClientAnnotationsSameTrait.
 */
trait AssertClientAnnotationsSameTrait
{
    public static function assertClientAnnotationEquals(string $expectedIdentifier, TokenSource|string $expectedTokenSource, AbstractClient|ClientTokenResponseInterface $actual, string $message = '')
    {
        $actual->setReader(new AnnotationReader());
        self::assertEquals($expectedIdentifier, $actual->getIdentifier(), $message);
        self::assertEquals($expectedTokenSource instanceof TokenSource ? $expectedTokenSource : TokenSource::from($expectedTokenSource), $actual->getTokenSource(), $message);
    }

    public function assertUsesClientAnnotations(AbstractClient|ClientTokenResponseInterface $actual)
    {
        $reader = $this->getMockBuilder(Reader::class)->getMock();

        $reader->expects($this->exactly(2))
            ->method('getClassAnnotation');

        $actual->setReader($reader);

        $actual->getIdentifier();
        $actual->getTokenSource();

        $actual->setReader(new AnnotationReader());
        $this->assertNotEmpty($actual->getIdentifier());
        $this->assertNotEmpty($actual->getTokenSource());
    }

    public function assertNotUsesClientAnnotations(AbstractClient|ClientTokenResponseInterface $actual)
    {
        $reader = $this->getMockBuilder(Reader::class)->getMock();

        $reader->expects($this->never())
            ->method('getClassAnnotation');

        $actual->setReader($reader);

        $this->assertNotEmpty($actual->getIdentifier());
        $this->assertNotEmpty($actual->getTokenSource());
    }
}
