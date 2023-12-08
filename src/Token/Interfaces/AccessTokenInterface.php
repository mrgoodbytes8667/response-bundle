<?php

namespace Bytes\ResponseBundle\Token\Interfaces;

use Bytes\ResponseBundle\Enums\TokenSource;
use DateInterval;

/**
 * Interface AccessTokenInterface.
 */
interface AccessTokenInterface
{
    public function getAccessToken(): ?string;

    public function getRefreshToken(): ?string;

    /**
     * @return DateInterval|int|null
     */
    public function getExpiresIn();

    /**
     * @return string[]|string|null
     */
    public function getScope();

    public function getTokenType(): ?string;

    /**
     * @return TokenSource|string|null
     */
    public function getTokenSource();

    public function getIdentifier(): ?string;

    public static function createFromAccessToken(AccessTokenInterface|string $token): static;

    public static function createFromParts(...$args): static;

    /**
     * @return $this
     */
    public function updateFromAccessToken(AccessTokenInterface $token);
}
