<?php

namespace Bytes\ResponseBundle\HttpClient\Token;

use Bytes\ResponseBundle\Routing\OAuthInterface;

/**
 * Interface TokenClientInterface.
 *
 * @experimental
 */
interface TokenClientInterface extends TokenExchangeInterface, TokenRefreshInterface, TokenRevokeInterface, TokenValidateInterface
{
    /**
     * @return $this
     */
    public function setOAuth(?OAuthInterface $oAuth);
}
