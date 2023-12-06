<?php

namespace Bytes\ResponseBundle\Annotations;

use Attribute;
use Bytes\ResponseBundle\Enums\TokenSource;

#[Attribute(Attribute::TARGET_CLASS)]
class Client
{
    use ClientTrait;

    public function __construct(array $values = [], string $identifier = null, TokenSource|string $tokenSource = null)
    {
        if (isset($values['value'])) {
            $values['identifier'] = $values['value'];
            unset($values['value']);
        }
        if (!is_null($identifier)) {
            $values['identifier'] = $identifier;
        }
        if (!is_null($tokenSource)) {
            $values['tokenSource'] = $tokenSource;
        }

        $this->set(...$values);
    }

    /**
     * @return $this
     */
    public function set(string $identifier = null, TokenSource|string $tokenSource = null): self
    {
        $this->setIdentifier($identifier);
        $this->setTokenSource($tokenSource);

        return $this;
    }
}
