<?php


namespace Bytes\ResponseBundle\Event;


use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class TokenChangedEvent
 * Fired when a token changes, either because a new token has been created, or a token has been refreshed.
 * @package Bytes\ResponseBundle\Event
 *
 * @experimental
 */
class TokenChangedEvent extends Event
{
    /**
     * @Event("Bytes\ResponseBundle\Event\TokenChangedEvent")
     */
    public const NAME = 'bytes_response.token.changed';

    /**
     * @var string = ['NEW', 'REFRESHED'][$any]
     */
    private $type = 'NEW';

    /**
     * TokenChangedEvent constructor.
     * @param AccessTokenInterface $token
     * @param AccessTokenInterface|null $oldToken
     */
    public function __construct(private AccessTokenInterface $token, private ?AccessTokenInterface $oldToken = null)
    {
        if (!empty($oldToken) && !empty($token->getRefreshToken())) {
            $this->type = 'REFRESHED';
        }
    }

    /**
     * @param AccessTokenInterface $token
     * @param AccessTokenInterface|null $oldToken
     * @return static
     */
    #[Pure] public static function new(AccessTokenInterface $token, ?AccessTokenInterface $oldToken = null): static
    {
        return new static($token, $oldToken);
    }

    /**
     * @return string = ['NEW', 'REFRESHED'][$any]
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return AccessTokenInterface
     */
    public function getToken(): AccessTokenInterface
    {
        return $this->token;
    }

    /**
     * @param AccessTokenInterface $token
     * @return $this
     */
    public function setToken(AccessTokenInterface $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return AccessTokenInterface|null
     */
    public function getOldToken(): ?AccessTokenInterface
    {
        return $this->oldToken;
    }

    /**
     * @param AccessTokenInterface|null $oldToken
     * @return $this
     */
    public function setOldToken(?AccessTokenInterface $oldToken): self
    {
        $this->oldToken = $oldToken;
        return $this;
    }
}