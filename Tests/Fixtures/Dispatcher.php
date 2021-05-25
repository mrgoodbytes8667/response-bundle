<?php


namespace Bytes\ResponseBundle\Tests\Fixtures;


use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Event\DispatcherTrait;
use Bytes\ResponseBundle\Event\EventDispatcherTrait;
use Bytes\ResponseBundle\Event\RevokeTokenEvent;
use Bytes\ResponseBundle\Event\TokenGrantedEvent;
use Bytes\ResponseBundle\Event\TokenRevokedEvent;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class Dispatcher
{
    use EventDispatcherTrait, DispatcherTrait;


    /**
     * Dispatcher constructor.
     */
    public function __construct()
    {
        $this->setDispatcher(new EventDispatcher());
    }

    public function triggerFakeEvent(Event $event, string $name = null)
    {
        return $this->dispatch($event, $name);
    }

    public function dispatchTokenEvents($token)
    {
        return [
            'refreshTokenEvent' => $this->dispatchRefreshTokenEvent($token),
            'revokeTokenEvent' => $this->dispatchRevokeTokenEvent($token),
            'validateTokenEvent' => $this->dispatchValidateTokenEvent($token),
            'tokenGrantedEvent' => $this->dispatchTokenGrantedEvent($token),
            'tokenRevokedEvent' => $this->dispatchTokenRevokedEvent($token),
            'tokenRefreshedEvent' => $this->dispatchTokenRefreshedEvent($token, $token),
        ];
    }

    public function dispatchObtainValidToken(string $identifier, TokenSource $tokenSource, ?UserInterface $user = null, array $scopes = [])
    {
        return $this->dispatchObtainValidTokenEvent($identifier, $tokenSource, $user, $scopes);
    }

    public function dispatchTokenValidated(AccessTokenInterface $token, TokenValidationResponseInterface $validation)
    {
        return $this->dispatchTokenValidatedEvent($token, $validation);
    }
}
