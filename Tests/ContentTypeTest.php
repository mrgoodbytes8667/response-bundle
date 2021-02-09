<?php

namespace Bytes\ResponseBundle\Tests;

use Bytes\ResponseBundle\Enums\ContentType;
use PHPUnit\Framework\TestCase;
use Spatie\Enum\Phpunit\EnumAssertions;

/**
 * Class ContentTypeTest
 * @package Bytes\ResponseBundle\Tests
 */
class ContentTypeTest extends TestCase
{
    /**
     *
     */
    public function testImageJpg()
    {
        EnumAssertions::assertIsEnum(ContentType::imageJpg());
        EnumAssertions::assertEqualsEnum(ContentType::imageJpg(), 'image/jpeg');
        EnumAssertions::assertSameEnumValue(ContentType::imageJpg(), 'image/jpeg');
        EnumAssertions::assertSameEnumLabel(ContentType::imageJpg(), 'imageJpg');
    }

    /**
     *
     */
    public function testImagePng()
    {
        EnumAssertions::assertIsEnum(ContentType::imagePng());
        EnumAssertions::assertEqualsEnum(ContentType::imagePng(), 'image/png');
        EnumAssertions::assertSameEnumValue(ContentType::imagePng(), 'image/png');
        EnumAssertions::assertSameEnumLabel(ContentType::imagePng(), 'imagePng');
    }
}
