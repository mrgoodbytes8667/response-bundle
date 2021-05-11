<?php


namespace Bytes\ResponseBundle\HttpClient\Token;


use Bytes\ResponseBundle\Routing\OAuthInterface;

/**
 * Interface TokenClientInterface
 * @package Bytes\ResponseBundle\HttpClient\Token
 *
 * @experimental
 */
interface TokenClientInterface extends TokenExchangeInterface, TokenRefreshInterface, TokenRevokeInterface, TokenValidateInterface
{
    /**
     * @param OAuthInterface|null $oAuth
     * @return $this
     */
    public function setOAuth(?OAuthInterface $oAuth);
}