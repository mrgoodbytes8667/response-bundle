<?php


namespace Bytes\ResponseBundle\HttpClient\Token;


/**
 * Interface TokenClientInterface
 * @package Bytes\ResponseBundle\HttpClient\Token
 *
 * @experimental
 */
interface TokenClientInterface extends TokenExchangeInterface, TokenRefreshInterface, TokenRevokeInterface, TokenValidateInterface
{

}