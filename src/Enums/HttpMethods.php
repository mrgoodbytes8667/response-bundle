<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\BackedEnumTrait;
use Bytes\EnumSerializerBundle\Enums\EasyAdminChoiceEnumInterface;
use JetBrains\PhpStorm\Deprecated;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods As of 2020-10-29
 */
enum HttpMethods: string implements EasyAdminChoiceEnumInterface
{
    use BackedEnumTrait;

    case get = 'GET';     // The GET method requests a representation of the specified resource. Requests using GET should only retrieve data.
    case head = 'HEAD';    // The HEAD method asks for a response identical to that of a GET request; but without the response body.
    case post = 'POST';    // The POST method is used to submit an entity to the specified resource; often causing a change in state or side effects on the server.
    case put = 'PUT';     // The PUT method replaces all current representations of the target resource with the request payload.
    case delete = 'DELETE';  // The DELETE method deletes the specified resource.
    case connect = 'CONNECT'; // The CONNECT method establishes a tunnel to the server identified by the target resource.
    case options = 'OPTIONS'; // he OPTIONS method is used to describe the communication options for the target resource.
    case trace = 'TRACE';   // The TRACE method performs a message loop-back test along the path to the target resource.
    case patch = 'PATCH';   // The PATCH method is used to apply partial modifications to a resource.

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function get(): HttpMethods
    {
        return HttpMethods::get;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function head(): HttpMethods
    {
        return HttpMethods::head;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function post(): HttpMethods
    {
        return HttpMethods::post;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function put(): HttpMethods
    {
        return HttpMethods::put;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function delete(): HttpMethods
    {
        return HttpMethods::delete;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function connect(): HttpMethods
    {
        return HttpMethods::connect;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function options(): HttpMethods
    {
        return HttpMethods::options;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function trace(): HttpMethods
    {
        return HttpMethods::trace;
    }

    #[Deprecated(reason: 'since 3.2.0, use "%name%" instead.', replacement: '%class%::%name%')]
    public static function patch(): HttpMethods
    {
        return HttpMethods::patch;
    }

}
