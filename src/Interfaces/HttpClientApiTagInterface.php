<?php

namespace Bytes\ResponseBundle\Interfaces;

use Bytes\ResponseBundle\HttpClient\Api\AbstractApiClient;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * For {@see AbstractApiClient} classes that autoconfigure the bytes_response.http_client.api tag
 */
interface HttpClientApiTagInterface
{
    public function setSecurity(?Security $security);
}
