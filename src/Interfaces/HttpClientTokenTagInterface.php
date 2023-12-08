<?php

namespace Bytes\ResponseBundle\Interfaces;

use Bytes\ResponseBundle\HttpClient\Api\AbstractApiClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * For {@see AbstractApiClient} classes that autoconfigure the bytes_response.http_client.token tag.
 */
interface HttpClientTokenTagInterface
{
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator);
}
