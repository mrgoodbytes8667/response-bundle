<?php

namespace Bytes\ResponseBundle\Tests;

use Bytes\EnumSerializerBundle\Phpunit\EnumAssertions;
use Bytes\ResponseBundle\Enums\ContentType;
use PHPUnit\Framework\TestCase;

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
    }

    /**
     *
     */
    public function testImagePng()
    {
        EnumAssertions::assertIsEnum(ContentType::imagePng());
        EnumAssertions::assertEqualsEnum(ContentType::imagePng(), 'image/png');
        EnumAssertions::assertSameEnumValue(ContentType::imagePng(), 'image/png');
    }
}
