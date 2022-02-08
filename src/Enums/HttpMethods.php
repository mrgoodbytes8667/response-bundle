<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\BackedEnumInterface;
use Bytes\EnumSerializerBundle\Enums\BackedEnumTrait;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods As of 2020-10-29
 */
enum HttpMethods: string implements BackedEnumInterface
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
}
