<?php

namespace Bytes\ResponseBundle\Request;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

interface SignatureInterface
{
    /**
     * Is the signature valid?
     *
     * @param bool|resource|string|null $content
     * @param bool                      $throw   if true, will throw exceptions instead of simply returning false
     *
     * @throws AccessDeniedHttpException
     */
    public function validateHubSignature(HeaderBag $headers, $content, bool $throw = true): bool;
}
