<?php

namespace Bytes\ResponseBundle\HttpClient;

use Bytes\ResponseBundle\Annotations\Client;
use Bytes\ResponseBundle\Enums\TokenSource;
use ReflectionAttribute;
use ReflectionClass;

trait ClientTrait
{
    /**
     * @var string
     */
    private $cachedIdentifier;

    /**
     * @var TokenSource
     */
    private $cachedTokenSource;

    protected function setClientAnnotations()
    {
        $reflectionClass = new ReflectionClass(static::class);
        $classAttributes = $reflectionClass->getAttributes(Client::class, ReflectionAttribute::IS_INSTANCEOF);
        /** @var Client|null $annotations */
        $annotations = null;
        if (!empty($classAttributes)) {
            $annotations = $classAttributes[0]->newInstance();
        }

        if (!empty($annotations)) {
            $this->cachedIdentifier = $annotations?->getIdentifier();
            $this->cachedTokenSource = $annotations?->getTokenSource();
        }
    }

    public function getIdentifier(): ?string
    {
        if (property_exists(static::class, 'identifier')) {
            $this->cachedIdentifier = static::$identifier;
        }

        if (is_null($this->cachedIdentifier)) {
            $this->setClientAnnotations();
        }

        return $this->cachedIdentifier;
    }

    public function getTokenSource(): ?TokenSource
    {
        if (property_exists(static::class, 'tokenSource') && (!empty(static::$tokenSource) && is_string(static::$tokenSource) && TokenSource::isValid(static::$tokenSource))) {
            $this->cachedTokenSource = TokenSource::from(static::$tokenSource);
        }

        if (is_null($this->cachedTokenSource)) {
            $this->setClientAnnotations();
        }

        return $this->cachedTokenSource;
    }
}
