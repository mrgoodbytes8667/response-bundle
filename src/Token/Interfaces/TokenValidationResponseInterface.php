<?php


namespace Bytes\ResponseBundle\Token\Interfaces;


use InvalidArgumentException;

/**
 * Interface TokenValidationResponseInterface
 * @package Bytes\ResponseBundle\Token\Interfaces
 *
 * @experimental
 */
interface TokenValidationResponseInterface
{
    /**
     * @param ...$args
     * @return static
     */
    public static function create(...$args): static;

    /**
     * @return string|null
     */
    public function getClientId(): ?string;

    /**
     * @return string|null
     */
    public function getUserName(): ?string;

    /**
     * @return string[]|null
     */
    public function getScopes(): ?array;

    /**
     * @return string|null
     */
    public function getUserId(): ?string;

    /**
     * @param string|null $clientId
     * @return bool
     */
    public function hasMatchingClientId(?string $clientId): bool;

    /**
     * @param string|null $id
     * @return bool
     */
    public function hasMatchingUserId(?string $id): bool;

    /**
     * @param string|null $userName
     * @return bool
     */
    public function hasMatchingUserName(?string $userName): bool;

    /**
     * @param ...$args
     * @return bool
     */
    public function isMatch(...$args): bool;

    /**
     * Is this token an app/bot token?
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function isAppToken(): bool;
}