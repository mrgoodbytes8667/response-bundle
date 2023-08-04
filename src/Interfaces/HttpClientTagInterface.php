<?php

namespace Bytes\ResponseBundle\Interfaces;

use Bytes\ResponseBundle\HttpClient\Api\AbstractApiClient;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * For {@see AbstractApiClient} classes that autoconfigure the bytes_response.http_client tag
 */
interface HttpClientTagInterface
{
    public function setSerializer(SerializerInterface $serializer);

    public function setValidator(ValidatorInterface $validator);

    public function setReader(Reader $reader);
}
