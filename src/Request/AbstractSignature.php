<?php

namespace Bytes\ResponseBundle\Request;

use Bytes\ResponseBundle\Handler\LocatorInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class AbstractSignature implements LocatorInterface
{
    /**
     * AbstractSignature constructor.
     */
    public function __construct(protected string $secret)
    {
    }

    public static function getSignatureField(): string
    {
        return 'x-hub-signature';
    }

    /**
     * Is the signature valid?
     *
     * @param bool|resource|string|null $content
     * @param bool                      $throw   if true, will throw exceptions instead of simply returning false
     *
     * @throws AccessDeniedHttpException
     *
     * @see https://gist.github.com/milo/daed6e958ea534e4eba3
     */
    public function validateHubSignature(HeaderBag $headers, $content, bool $throw = true): bool
    {
        // Was the hub-signature supplied? If not, exit.
        if (empty($headers->get(static::getSignatureField()))) {
            if ($throw) {
                throw new AccessDeniedHttpException(sprintf("HTTP header '%s' is missing.", static::getSignatureField()));
            } else {
                return false;
            }
        }

        if (!$this->hasHashExtension()) {
            if ($throw) {
                throw new AccessDeniedHttpException("Missing 'hash' extension to check the secret code validity.");
            } else {
                return false;
            }
        }

        list($algo, $hash) = explode('=', $headers->get(static::getSignatureField()), 2) + ['', ''];
        if (!in_array($algo, hash_algos(), true)) {
            if ($throw) {
                throw new AccessDeniedHttpException("Hash algorithm '$algo' is not supported.");
            } else {
                return false;
            }
        }

        $check = $this->getHashString($headers, $content);

        if ($hash !== hash_hmac($algo, $check, $this->secret)) {
            if ($throw) {
                throw new AccessDeniedHttpException('Hook secret does not match.');
            } else {
                return false;
            }
        }

        return true;
    }

    protected function getHashString(HeaderBag $headers, bool|string|null $content): string
    {
        return $content;
    }

    protected function hasHashExtension(): bool
    {
        return extension_loaded('hash');
    }
}
