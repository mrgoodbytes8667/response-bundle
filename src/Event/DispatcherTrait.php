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
 * Trait DispatcherTrait
 * @package Bytes\ResponseBundle\Event
 *
 * @property EventDispatcherInterface $dispatcher
 */
trait DispatcherTrait
{
    /**
     * @param StoppableEventInterface $event
     * @param string|null $eventName
     * @return mixed
     */
    protected function dispatch(StoppableEventInterface $event, string $eventName = null)
    {
        if(empty($eventName)) {
            $eventName = get_class($event);
        }
        return $this->dispatcher->dispatch($event, $eventName);
    }

    /**
     * @param AccessTokenInterface $token
     * @return RefreshTokenEvent
     */
    protected function dispatchRefreshTokenEvent(AccessTokenInterface $token)
    {
        return $this->dispatch(RefreshTokenEvent::new($token));
    }

    /**
     * @param ApiClientInterface $client
     * @param string $method
     * @param string $url
     * @param AsyncContext $context
     * @param array $options
     * @param string|null $content
     * @param int|null $retryCount
     * @param bool $shouldRetry
     *
     * @return ApiRetryEvent
     */
    protected function dispatchApiRetryEvent(ApiClientInterface $client, string $method, string $url, AsyncContext $context, array $options = [], ?string $content = null, ?int $retryCount = 0, bool $shouldRetry = true){
        return $this->dispatch(ApiRetryEvent::new(client: $client, method: $method, url: $url, options: $options, context: $context, responseContent: $content, retryCount: $retryCount, shouldRetry: $shouldRetry));
    }

    /**
     * @param string $identifier
     * @param TokenSource $tokenSource
     * @param UserInterface|null $user
     * @param array $scopes
     * @return ObtainValidTokenEvent
     */
    protected function dispatchObtainValidTokenEvent(string $identifier, TokenSource $tokenSource, ?UserInterface $user = null, array $scopes = []){
        return $this->dispatch(ObtainValidTokenEvent::new(identifier: $identifier, tokenSource: $tokenSource, user: $user, scopes: $scopes));
    }

    /**
     * @param AccessTokenInterface $token
     * @return RevokeTokenEvent
     */
    protected function dispatchRevokeTokenEvent(AccessTokenInterface $token){
        return $this->dispatch(RevokeTokenEvent::new(token: $token));
    }

    /**
     * @param AccessTokenInterface $token
     * @return TokenGrantedEvent
     */
    protected function dispatchTokenGrantedEvent(AccessTokenInterface $token){
        return $this->dispatch(TokenGrantedEvent::new(token: $token));
    }

    /**
     * @param AccessTokenInterface $token
     * @param AccessTokenInterface|null $oldToken
     * @return TokenRefreshedEvent
     */
    protected function dispatchTokenRefreshedEvent(AccessTokenInterface $token, ?AccessTokenInterface $oldToken){
        return $this->dispatch(TokenRefreshedEvent::new(token: $token, oldToken: $oldToken));
    }

    /**
     * @param AccessTokenInterface $token
     * @return TokenRevokedEvent
     */
    protected function dispatchTokenRevokedEvent(AccessTokenInterface $token){
        return $this->dispatch(TokenRevokedEvent::new(token: $token));
    }

    /**
     * @param AccessTokenInterface $token
     * @param TokenValidationResponseInterface $validation
     * @return TokenValidatedEvent
     */
    protected function dispatchTokenValidatedEvent(AccessTokenInterface $token, TokenValidationResponseInterface $validation, ?UserInterface $user = null){
        return $this->dispatch(TokenValidatedEvent::new(token: $token, validation: $validation, user: $user));
    }
}