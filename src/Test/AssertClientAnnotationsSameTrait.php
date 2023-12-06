<?php

namespace Bytes\ResponseBundle\Test;

use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\HttpClient\AbstractClient;
use Bytes\ResponseBundle\Interfaces\ClientTokenResponseInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;

/**
 * @deprecated Annotation support will be dropped in v6.0.0
 */
trait AssertClientAnnotationsSameTrait
{
    /**
     * @deprecated Annotation support will be dropped in v6.0.0
     */
    public static function assertClientAnnotationEquals(string $expectedIdentifier, TokenSource|string $expectedTokenSource, AbstractClient|ClientTokenResponseInterface $actual, string $message = '')
    {
        trigger_deprecation('mrgoodbytes8667/response-bundle', '5.4.0', 'Annotation support will be dropped in v6.0.0.');

        $actual->setReader(new AnnotationReader());
        self::assertEquals($expectedIdentifier, $actual->getIdentifier(), $message);
        self::assertEquals($expectedTokenSource instanceof TokenSource ? $expectedTokenSource : TokenSource::from($expectedTokenSource), $actual->getTokenSource(), $message);
    }

    /**
     * @deprecated Annotation support will be dropped in v6.0.0
     */
    public function assertUsesClientAnnotations(AbstractClient|ClientTokenResponseInterface $actual)
    {
        trigger_deprecation('mrgoodbytes8667/response-bundle', '5.4.0', 'Annotation support will be dropped in v6.0.0.');

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

    /**
     * @deprecated Annotation support will be dropped in v6.0.0
     */
    public function assertNotUsesClientAnnotations(AbstractClient|ClientTokenResponseInterface $actual)
    {
        trigger_deprecation('mrgoodbytes8667/response-bundle', '5.4.0', 'Annotation support will be dropped in v6.0.0.');

        $reader = $this->getMockBuilder(Reader::class)->getMock();

        $reader->expects($this->never())
            ->method('getClassAnnotation');

        $actual->setReader($reader);

        $this->assertNotEmpty($actual->getIdentifier());
        $this->assertNotEmpty($actual->getTokenSource());
    }
}
