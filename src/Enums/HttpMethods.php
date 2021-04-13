<?php


namespace Bytes\ResponseBundle\Enums;


use Bytes\EnumSerializerBundle\Enums\Enum;

/**
 * Class HttpMethods
 * @package Bytes\ResponseBundle\Enums
 *
 * @method static self get() The GET method requests a representation of the specified resource. Requests using GET should only retrieve data.
 * @method static self head() The HEAD method asks for a response identical to that of a GET request, but without the response body.
 * @method static self post() The POST method is used to submit an entity to the specified resource, often causing a change in state or side effects on the server.
 * @method static self put() The PUT method replaces all current representations of the target resource with the request payload.
 * @method static self delete() The DELETE method deletes the specified resource.
 * @method static self connect() The CONNECT method establishes a tunnel to the server identified by the target resource.
 * @method static self options()The OPTIONS method is used to describe the communication options for the target resource.
 * @method static self trace() The TRACE method performs a message loop-back test along the path to the target resource.
 * @method static self patch() The PATCH method is used to apply partial modifications to a resource.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods As of 2020-10-29
 */
class HttpMethods extends Enum
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'get' => 'GET',
            'head' => 'HEAD',
            'post' => 'POST',
            'put' => 'PUT',
            'delete' => 'DELETE',
            'connect' => 'CONNECT',
            'options' => 'OPTIONS',
            'trace' => 'TRACE',
            'patch' => 'PATCH',
        ];
    }
}