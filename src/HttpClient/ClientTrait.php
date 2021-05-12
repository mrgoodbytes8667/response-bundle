<?php


namespace Bytes\ResponseBundle\HttpClient;


use Bytes\ResponseBundle\Annotations\AnnotationReaderTrait;
use Bytes\ResponseBundle\Annotations\Client;
use Bytes\ResponseBundle\Enums\TokenSource;

/**
 * Trait ClientTrait
 * @package Bytes\ResponseBundle\HttpClient
 */
trait ClientTrait
{
    use AnnotationReaderTrait;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var TokenSource
     */
    private $tokenSource;

    /**
     *
     */
    protected function setClientAnnotations()
    {
        if(is_null($this->reader))
        {
            throw new \LogicException('"setReader()" must be called before attempting to load client annotations.');
        }
        $reflectionClass = new \ReflectionClass(static::class);
        /** @var Client $annotations */
        $annotations = $this->reader->getClassAnnotation($reflectionClass, Client::class);
        if(!empty($annotations))
        {
            $this->identifier = $annotations?->getIdentifier();
            $this->tokenSource = $annotations?->getTokenSource();
        }
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        if(property_exists(static::class, 'identifier')) {
            $this->identifier = static::$identifier;
        }

        if(is_null($this->identifier))
        {
            $this->setClientAnnotations();
        }
        return $this->identifier;
    }

    /**
     * @return TokenSource|null
     */
    public function getTokenSource(): ?TokenSource
    {
        if(property_exists(static::class, 'tokenSource')) {
            if(!empty(static::$tokenSource) && is_string(static::$tokenSource) && TokenSource::isValid(static::$tokenSource)) {
                $this->tokenSource = TokenSource::from(static::$tokenSource);
            }
        }
        if(is_null($this->tokenSource))
        {
            $this->setClientAnnotations();
        }
        return $this->tokenSource;
    }

}