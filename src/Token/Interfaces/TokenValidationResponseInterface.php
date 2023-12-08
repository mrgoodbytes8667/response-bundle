<?php

namespace Bytes\ResponseBundle\Token\Interfaces;

use DateTimeInterface;
use InvalidArgumentException;

/**
 * Interface TokenValidationResponseInterface.
 *
 * @experimental
 */
interface TokenValidationResponseInterface
{
    public static function create(...$args): static;

    public function getClientId(): ?string;

    public function getUserName(): ?string;

    /**
     * @return string[]|null
     */
    public function getScopes(): ?array;

    public function getUserId(): ?string;

    public function hasMatchingClientId(?string $clientId): bool;

    public function hasMatchingUserId(?string $id): bool;

    public function hasMatchingUserName(?string $userName): bool;

    public function isMatch(...$args): bool;

    public function getExpiresAt(): ?DateTimeInterface;

    public function hasExpired(): bool;

    /**
     * Is this token an app/bot token?
     *
     * @throws InvalidArgumentException
     */
    public function isAppToken(): bool;
}
