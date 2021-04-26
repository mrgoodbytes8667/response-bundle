<?php


namespace Bytes\ResponseBundle\HttpClient\Token;


use Bytes\ResponseBundle\Token\Interfaces\TokenValidateInterface;

/**
 * Interface TokenClientInterface
 * @package Bytes\ResponseBundle\HttpClient\Token
 *
 * @experimental
 */
interface TokenClientInterface extends TokenExchangeInterface, TokenRefreshInterface, TokenRevokeInterface, TokenValidateInterface
{

}