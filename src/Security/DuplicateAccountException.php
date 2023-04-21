<?php


namespace Bytes\ResponseBundle\Security;


use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Throwable;

/**
 * Class DuplicateAccountException
 * @package Bytes\ResponseBundle\Security
 */
class DuplicateAccountException extends AuthenticationException
{
    /**
     * DuplicateAccountException constructor.
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(empty($message) ? AbstractOAuthAuthenticator::REDIRECT_TO_LOGOUT : $message, $code, $previous);
    }

    /**
     * Message key to be used by the translation component.
     *
     * @return string
     */
    public function getMessageKey(): string
    {
        return 'Account is already associated with another user.';
    }
}