<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\StringBackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\StringBackedEnumTrait;
use JetBrains\PhpStorm\Deprecated;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods As of 2020-10-29
 */
enum HttpMethods: string implements StringBackedEnumInterface
{
    use StringBackedEnumTrait;

    case get = 'GET';     // The GET method requests a representation of the specified resource. Requests using GET should only retrieve data.
    case head = 'HEAD';    // The HEAD method asks for a response identical to that of a GET request; but without the response body.
    case post = 'POST';    // The POST method is used to submit an entity to the specified resource; often causing a change in state or side effects on the server.
    case put = 'PUT';     // The PUT method replaces all current representations of the target resource with the request payload.
    case delete = 'DELETE';  // The DELETE method deletes the specified resource.
    case connect = 'CONNECT'; // The CONNECT method establishes a tunnel to the server identified by the target resource.
    case options = 'OPTIONS'; // he OPTIONS method is used to describe the communication options for the target resource.
    case trace = 'TRACE';   // The TRACE method performs a message loop-back test along the path to the target resource.
    case patch = 'PATCH';   // The PATCH method is used to apply partial modifications to a resource.

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::get')]
    public static function get() {
        return static::get;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::head')]
    public static function head() {
        return static::head;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::post')]
    public static function post() {
        return static::post;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::put')]
    public static function put() {
        return static::put;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::delete')]
    public static function delete() {
        return static::delete;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::connect')]
    public static function connect() {
        return static::connect;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::options')]
    public static function options() {
        return static::options;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::trace')]
    public static function trace() {
        return static::trace;
    }

    #[Deprecated(reason: 'since 5.0.0, use the enumeration constant instead.', replacement: '%class%::patch')]
    public static function patch() {
        return static::patch;
    }
}
