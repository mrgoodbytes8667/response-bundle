<?php


namespace Bytes\ResponseBundle\Token\Interfaces;


/**
 * Interface TokenValidationResponseInterface
 * @package Bytes\ResponseBundle\Token\Interfaces
 *
 * @experimental
 */
interface TokenValidationResponseInterface
{
    /**
     * @return string|null
     */
    public function getClientId(): ?string;

    /**
     * @param string|null $clientId
     * @return $this
     */
    public function setClientId(?string $clientId);

    /**
     * @return string|null
     */
    public function getUserName(): ?string;

    /**
     * @param string|null $userName
     * @return $this
     */
    public function setUserName(?string $userName);

    /**
     * @return string[]|null
     */
    public function getScopes(): ?array;

    /**
     * @param string[]|null $scopes
     * @return $this
     */
    public function setScopes(?array $scopes);

    /**
     * @return string|null
     */
    public function getUserId(): ?string;

    /**
     * @param string|null $userId
     * @return $this
     */
    public function setUserId(?string $userId);

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
     * @param ...$args
     * @return static
     */
    public static function create(...$args): static;
}