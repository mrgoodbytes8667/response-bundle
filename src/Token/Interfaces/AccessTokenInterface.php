<?php


namespace Bytes\ResponseBundle\Token\Interfaces;


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
}