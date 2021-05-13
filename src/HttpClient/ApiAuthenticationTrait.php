<?php


namespace Bytes\ResponseBundle\HttpClient;


use Bytes\ResponseBundle\Annotations\Auth;
use Bytes\ResponseBundle\Token\Exceptions\NoTokenException;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;

trait ApiAuthenticationTrait
{
    /**
     * @param Auth|null $auth
     * @return AccessTokenInterface|null
     * @throws NoTokenException
     */
    abstract protected function getToken(?Auth $auth = null): ?AccessTokenInterface;

    /**
     * @param Auth|null $auth
     * @return array
     * @throws NoTokenException
     */
    protected function getAuthenticationOption(?Auth $auth = null)
    {
        $token = $this->getToken();
        if(!empty($token))
        {
            return ['auth_bearer' => $token->getAccessToken()];
        }

        return [];
    }
}