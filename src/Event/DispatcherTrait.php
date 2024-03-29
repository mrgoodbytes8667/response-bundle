<?php

namespace Bytes\ResponseBundle\Event;

use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\HttpClient\ApiClientInterface;
use Bytes\ResponseBundle\Token\Interfaces\AccessTokenInterface;
use Bytes\ResponseBundle\Token\Interfaces\TokenValidationResponseInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Trait DispatcherTrait.
 *
 * @property EventDispatcherInterface $dispatcher
 */
trait DispatcherTrait
{
    protected function dispatch(StoppableEventInterface $event, string $eventName = null)
    {
        if (empty($eventName)) {
            $eventName = get_class($event);
        }

        return $this->dispatcher->dispatch($event, $eventName);
    }

    /**
     * @return RefreshTokenEvent
     */
    protected function dispatchRefreshTokenEvent(AccessTokenInterface $token)
    {
        return $this->dispatch(RefreshTokenEvent::new($token));
    }

    /**
     * @return ApiRetryEvent
     */
    protected function dispatchApiRetryEvent(ApiClientInterface $client, string $method, string $url, AsyncContext $context, array $options = [], string $content = null, ?int $retryCount = 0, bool $shouldRetry = true)
    {
        return $this->dispatch(ApiRetryEvent::new(client: $client, method: $method, url: $url, options: $options, context: $context, responseContent: $content, retryCount: $retryCount, shouldRetry: $shouldRetry));
    }

    /**
     * @return ObtainValidTokenEvent
     */
    protected function dispatchObtainValidTokenEvent(string $identifier, TokenSource $tokenSource, UserInterface $user = null, array $scopes = [])
    {
        return $this->dispatch(ObtainValidTokenEvent::new(identifier: $identifier, tokenSource: $tokenSource, user: $user, scopes: $scopes));
    }

    /**
     * @return RevokeTokenEvent
     */
    protected function dispatchRevokeTokenEvent(AccessTokenInterface $token)
    {
        return $this->dispatch(RevokeTokenEvent::new(token: $token));
    }

    /**
     * @return ValidateTokenEvent
     */
    protected function dispatchValidateTokenEvent(AccessTokenInterface $token, UserInterface $user = null)
    {
        return $this->dispatch(ValidateTokenEvent::new(token: $token, user: $user));
    }

    /**
     * @return TokenGrantedEvent
     */
    protected function dispatchTokenGrantedEvent(AccessTokenInterface $token)
    {
        return $this->dispatch(TokenGrantedEvent::new(token: $token));
    }

    /**
     * @return TokenRefreshedEvent
     */
    protected function dispatchTokenRefreshedEvent(AccessTokenInterface $token, ?AccessTokenInterface $oldToken)
    {
        return $this->dispatch(TokenRefreshedEvent::new(token: $token, oldToken: $oldToken));
    }

    /**
     * @return TokenRevokedEvent
     */
    protected function dispatchTokenRevokedEvent(AccessTokenInterface $token)
    {
        return $this->dispatch(TokenRevokedEvent::new(token: $token));
    }

    /**
     * @return TokenValidatedEvent
     */
    protected function dispatchTokenValidatedEvent(AccessTokenInterface $token, TokenValidationResponseInterface $validation, UserInterface $user = null)
    {
        return $this->dispatch(TokenValidatedEvent::new(token: $token, validation: $validation, user: $user));
    }
}
