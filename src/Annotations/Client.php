<?php


namespace Bytes\ResponseBundle\Annotations;


use Bytes\ResponseBundle\Enums\TokenSource;

/**
 * Class Client
 * @package Bytes\ResponseBundle\Annotations
 *
 * @Annotation
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Client
{
    use ClientTrait;

    /**
     * Client constructor.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        if (isset($values['value'])) {
            $values['identifier'] = $values['value'];
            unset($values['value']);
        }
        
        $this->set(...$values);
    }

    /**
     * @param string|null $identifier
     * @param TokenSource|string|null $tokenSource
     * @return $this
     */
    public function set(?string $identifier = null, TokenSource|string|null $tokenSource = null): self
    {
        $this->setIdentifier($identifier);
        $this->setTokenSource($tokenSource);
        return $this;
    }
}