<?php


namespace Bytes\ResponseBundle\Test\Constraint;


use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Objects\ComparableDateInterval;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use DateInterval;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * Class AccessTokenInterfaceSame
 * @package Bytes\ResponseBundle\Test\Constraint
 */
class AccessTokenInterfaceSame extends Constraint
{
    /**
     * AccessTokenInterfaceSame constructor.
     * @param AccessTokenInterface|null $token
     * @param string|null $accessToken
     * @param string|null $refreshToken
     * @param DateInterval|null $expiresIn
     * @param string|array|null $scope
     * @param string|null $tokenType
     * @param TokenSource|null $tokenSource
     * @param string|null $identifier
     */
    public function __construct(?AccessTokenInterface $token = null, private ?string $accessToken = null, private ?string $refreshToken = null, private ?DateInterval $expiresIn = null, private string|array|null $scope = null, private ?string $tokenType = null, private ?TokenSource $tokenSource = null, private ?string $identifier = null)
    {
        if (!empty($token)) {
            $this->accessToken = $this->accessToken ?: $token->getAccessToken();
            $this->refreshToken = $this->refreshToken ?: $token->getRefreshToken();
            $this->expiresIn = $this->expiresIn ?: $token->getExpiresIn();
            $this->scope = $this->scope ?: $token->getScope();
            $this->tokenType = $this->tokenType ?: $token->getTokenType();
            $this->tokenSource = $this->tokenSource ?: $token->getTokenSource();
            $this->identifier = $this->identifier ?: $token->getIdentifier();
        }
    }

    /**
     * @param AccessTokenInterface $token
     * @return static
     */
    public static function create(AccessTokenInterface $token): static
    {
        return new static(token: $token);
    }

    /**
     * @param ...$args = accessToken, refreshToken, expiresIn, scope, tokenType, tokenSource, identifier
     * @return static
     */
    public static function createFromParts(...$args): static
    {
        return new static(...$args);
    }

    /**
     * @param AccessTokenInterface $other
     *
     * {@inheritdoc}
     */
    protected function matches($other): bool
    {
        try {
            return $this->accessToken === $other->getAccessToken() &&
                $this->refreshToken === $other->getRefreshToken() &&
                ComparableDateInterval::create($this->expiresIn)->equals($other->getExpiresIn()) &&
                $this->scope === $other->getScope() &&
                $this->tokenType === $other->getTokenType() &&
                ($this->tokenSource->value == $other->getTokenSource() || $this->tokenSource == $other->getTokenSource()) &&
                $this->identifier === $other->getIdentifier();
        } catch (\Exception)
        {
            return false;
        }
    }

    /**
     * @param AccessTokenInterface $other
     *
     * {@inheritdoc}
     */
    protected function failureDescription($other): string
    {
        return $this->toString();
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return 'two objects are equal';
    }
}