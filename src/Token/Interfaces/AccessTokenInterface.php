<?php


namespace Bytes\ResponseBundle\Token\Interfaces;


use Bytes\ResponseBundle\Enums\TokenSource;
use DateInterval;

/**
 * Interface AccessTokenInterface
 * @package Bytes\ResponseBundle\Token\Interfaces
 */
interface AccessTokenInterface
{
    /**
     * @return string|null
     */
    public function getAccessToken(): ?string;

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string;

    /**
     * @return DateInterval|int|null
     */
    public function getExpiresIn();

    /**
     * @return string[]|string|null
     */
    public function getScope();

    /**
     * @return string|null
     */
    public function getTokenType(): ?string;

    /**
     * @return TokenSource|null
     */
    public function getTokenSource(): ?TokenSource;

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string;

    /**
     * @param AccessTokenInterface|string $token
     * @return static
     */
    public static function createFromAccessToken(AccessTokenInterface|string $token): static;

    /**
     * @param AccessTokenInterface $token
     * @return $this
     */
    public function updateFromAccessToken(AccessTokenInterface $token);
}