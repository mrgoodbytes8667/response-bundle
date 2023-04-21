<?php


namespace Bytes\ResponseBundle\Token;


use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Exception;

/**
 * Trait AccessTokenCreateUpdateFromTrait
 * @package Bytes\ResponseBundle\Token
 */
trait AccessTokenCreateUpdateFromTrait
{
    use AccessTokenTrait;

    /**
     * @param AccessTokenInterface|string $token
     * @return static
     * @throws Exception
     */
    public static function createFromAccessToken(AccessTokenInterface|string $token): static
    {
        $static = new static();
        if ($token instanceof AccessTokenInterface) {
            $static->updateFromAccessToken($token);
        } else {
            $static->setAccessToken($token);
        }
        
        return $static;
    }

    /**
     * Update the current access token with details from another access token (ie: a refresh token)
     * @param AccessTokenInterface $token
     * @return $this
     * @throws Exception
     */
    abstract public function updateFromAccessToken(AccessTokenInterface $token);
}