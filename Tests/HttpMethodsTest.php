<?php

namespace Bytes\ResponseBundle\Tests;

use Bytes\ResponseBundle\Enums\HttpMethods;
use PHPUnit\Framework\TestCase;
use Spatie\Enum\Phpunit\EnumAssertions;

class HttpMethodsTest extends TestCase
{

    public function testGet()
    {
        EnumAssertions::assertIsEnum(HttpMethods::get());
        EnumAssertions::assertEqualsEnum(HttpMethods::get(), 'get');
        EnumAssertions::assertSameEnumValue(HttpMethods::get(), 'GET');
        EnumAssertions::assertSameEnumLabel(HttpMethods::get(), 'get');
    }

    public function testConnect()
    {
        EnumAssertions::assertIsEnum(HttpMethods::connect());
        EnumAssertions::assertEqualsEnum(HttpMethods::connect(), 'connect');
        EnumAssertions::assertSameEnumValue(HttpMethods::connect(), 'CONNECT');
        EnumAssertions::assertSameEnumLabel(HttpMethods::connect(), 'connect');
    }

    public function testOptions()
    {
        EnumAssertions::assertIsEnum(HttpMethods::get());
        EnumAssertions::assertEqualsEnum(HttpMethods::get(), 'get');
        EnumAssertions::assertSameEnumValue(HttpMethods::get(), 'GET');
        EnumAssertions::assertSameEnumLabel(HttpMethods::get(), 'get');
    }

    public function testPatch()
    {
        EnumAssertions::assertIsEnum(HttpMethods::patch());
        EnumAssertions::assertEqualsEnum(HttpMethods::patch(), 'patch');
        EnumAssertions::assertSameEnumValue(HttpMethods::patch(), 'PATCH');
        EnumAssertions::assertSameEnumLabel(HttpMethods::patch(), 'patch');
    }

    public function testTrace()
    {
        EnumAssertions::assertIsEnum(HttpMethods::trace());
        EnumAssertions::assertEqualsEnum(HttpMethods::trace(), 'trace');
        EnumAssertions::assertSameEnumValue(HttpMethods::trace(), 'TRACE');
        EnumAssertions::assertSameEnumLabel(HttpMethods::trace(), 'trace');
    }

    public function testDelete()
    {
        EnumAssertions::assertIsEnum(HttpMethods::delete());
        EnumAssertions::assertEqualsEnum(HttpMethods::delete(), 'delete');
        EnumAssertions::assertSameEnumValue(HttpMethods::delete(), 'DELETE');
        EnumAssertions::assertSameEnumLabel(HttpMethods::delete(), 'delete');
    }

    public function testHead()
    {
        EnumAssertions::assertIsEnum(HttpMethods::head());
        EnumAssertions::assertEqualsEnum(HttpMethods::head(), 'head');
        EnumAssertions::assertSameEnumValue(HttpMethods::head(), 'HEAD');
        EnumAssertions::assertSameEnumLabel(HttpMethods::head(), 'head');
    }

    public function testPost()
    {
        EnumAssertions::assertIsEnum(HttpMethods::post());
        EnumAssertions::assertEqualsEnum(HttpMethods::post(), 'post');
        EnumAssertions::assertSameEnumValue(HttpMethods::post(), 'POST');
        EnumAssertions::assertSameEnumLabel(HttpMethods::post(), 'post');
    }

    public function testPut()
    {
        EnumAssertions::assertIsEnum(HttpMethods::put());
        EnumAssertions::assertEqualsEnum(HttpMethods::put(), 'put');
        EnumAssertions::assertSameEnumValue(HttpMethods::put(), 'PUT');
        EnumAssertions::assertSameEnumLabel(HttpMethods::put(), 'put');
    }
}
