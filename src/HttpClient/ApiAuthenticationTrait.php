<?php


namespace Bytes\ResponseBundle\HttpClient;


use Bytes\ResponseBundle\Annotations\Auth;
use Bytes\ResponseBundle\Event\ObtainValidTokenEvent;
use Bytes\ResponseBundle\Event\RefreshTokenEvent;
use Bytes\ResponseBundle\Security\SecurityTrait;
use Bytes\ResponseBundle\Token\Exceptions\NoTokenException;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Trait ApiAuthenticationTrait
 * @package Bytes\ResponseBundle\HttpClient
 *
 * @method dispatch(StoppableEventInterface $event, string $eventName = null)
 */
trait ApiAuthenticationTrait
{
    use ClientTrait, SecurityTrait;

    /**
     * @var AccessTokenInterface
     */
    private $token;

    /**
     * @param Auth|null $auth
     * @param bool $reset
     * @return AccessTokenInterface|null
     * @throws NoTokenException
     */
    protected function getToken(?Auth $auth = null, bool $reset = false): ?AccessTokenInterface
    {
        if($reset)
        {
            $this->resetToken();
        }
        if(!empty($this->token))
        {
            return $this->token;
        }

        /** @var ObtainValidTokenEvent $event */
        $event = $this->dispatch(ObtainValidTokenEvent::new($auth?->getIdentifier() ?? $this->getIdentifier(),
            $auth?->getTokenSource() ?? $this->getTokenSource(), $this->getTokenUser(), $auth?->getScopes() ?? []));
        if (!empty($event) && $event instanceof Event) {
            $this->token = $event?->getToken();
            return $this->token;
        }

        throw new NoTokenException();
    }

    /**
     * @return AccessTokenInterface|null
     * @throws NoTokenException
     */
    public function refreshToken(): ?AccessTokenInterface
    {
        if(empty($this->token))
        {
            throw new NoTokenException('No token was found to refresh.');
        }

        /** @var RefreshTokenEvent $event */
        $event = $this->dispatch(RefreshTokenEvent::new($this->token));
        if (!empty($event) && $event instanceof Event) {
            $this->token = $event?->getToken();
            return $this->token;
        }

        throw new NoTokenException('Token could not be refreshed.');
    }

    /**
     * @return $this
     */
    protected function resetToken(): self
    {
        $this->token = null;
        return $this;
    }

    /**
     * @param Auth|null $auth
     * @param bool $refresh
     * @return array
     * @throws NoTokenException
     */
    public function getAuthenticationOption(?Auth $auth = null, bool $refresh = false): array
    {
        if($refresh)
        {
            $token = $this->refreshToken();
        } else {
            $token = $this->getToken($auth);
        }
        if(!empty($token))
        {
            return ['auth_bearer' => $token->getAccessToken()];
        }

        return [];
    }
}